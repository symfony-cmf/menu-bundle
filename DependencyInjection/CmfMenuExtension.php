<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;

class CmfMenuExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $bundles = $container->getParameter('kernel.bundles');

        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('menu.xml');
        $factory = $container->getDefinition($this->getAlias().'.factory');
        $factory->replaceArgument(1, new Reference($config['content_url_generator']));
        $container->setParameter($this->getAlias() . '.allow_empty_items', $config['allow_empty_items']);

        $this->loadVoters($config, $loader, $container);

        if ($config['persistence']['phpcr']['enabled']) {
            $this->loadPhpcr($config['persistence']['phpcr'], $loader, $container);
        }

        if ($config['admin_extensions']['menu_options']['enabled']) {
            $this->loadExtensions($config, $loader, $container);
        }

        if ($config['publish_workflow']['enabled']) {
            $loader->load('publish-workflow.xml');
        }
    }

    public function loadVoters($config, XmlFileLoader $loader, ContainerBuilder $container)
    {
        $loader->load('voters.xml');

        if (isset($config['voters']['content_identity'])) {
            if (empty($config['voters']['content_identity']['content_key'])) {
                if (! class_exists('Symfony\\Cmf\\Bundle\\RoutingBundle\\Routing\\DynamicRouter')) {
                    throw new \RuntimeException('You need to set the content_key when not using the CmfRoutingBundle DynamicRouter');
                }
                $contentKey = DynamicRouter::CONTENT_KEY;
            } else {
                $contentKey = $config['voters']['content_identity']['content_key'];
            }
            $container->setParameter($this->getAlias() . '.content_key', $contentKey);
        } else {
            $container->removeDefinition('cmf_menu.current_item_voter.content_identity');
        }

        if (isset($config['voters']) && !array_key_exists('uri_prefix', $config['voters'])) {
            $container->removeDefinition('cmf_menu.current_item_voter.uri_prefix');
        }
    }

    public function loadPhpcr($config, XmlFileLoader $loader, ContainerBuilder $container)
    {
        $keys = array(
            'menu_document_class' => 'menu_document.class',
            'node_document_class' => 'node_document.class',
            'menu_admin_class' => 'menu_admin.class',
            'node_admin_class' => 'node_admin.class',
            'menu_basepath' => 'menu_basepath',
            'content_basepath' => 'content_basepath',
            'manager_name' => 'manager_name',
            'prefetch' => 'prefetch',
            'admin_recursive_breadcrumbs' => 'admin_recursive_breadcrumbs',
        );

        foreach ($keys as $sourceKey => $targetKey) {
            $container->setParameter(
                $this->getAlias() . '.persistence.phpcr.'. $targetKey,
                $config[$sourceKey]
            );
        }

        $loader->load('persistence-phpcr.xml');

        if ($config['use_sonata_admin']) {
            $this->loadSonataAdmin($config, $loader, $container);
        }
    }

    public function loadSonataAdmin($config, XmlFileLoader $loader, ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        if ('auto' === $config['use_sonata_admin'] && !isset($bundles['SonataDoctrinePHPCRAdminBundle'])) {
            return;
        }

        foreach (array('menu', 'node') as $key) {
            if (isset($config[$key.'_admin_class'])) {
                $container->setParameter(
                    $this->getAlias() . '.persistence.phpcr.'.$key.'_admin.class',
                    $config[$key.'_admin_class']
                );
            }
        }

        $loader->load('admin.xml');
    }

    public function loadExtensions($config, XmlFileLoader $loader, ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        if ('auto' === $config['admin_extensions']['menu_options']['enabled'] && !isset($bundles['SonataAdminBundle'])) {
            return;
        }

        if (!isset($bundles['SonataAdminBundle'])) {
            throw new InvalidConfigurationException('To use menu options extionsion, you need sonata-project/SonataAdminBundle in your project.');
        }

        if ($config['admin_extensions']['menu_options']['advanced'] && !isset($bundles['BurgovKeyValueFormBundle'])) {
            throw new InvalidConfigurationException('To use advanced menu options, you need the burgov/key-value-form-bundle in your project.');
        }

        $container->setParameter($this->getAlias() . '.admin_extensions.menu_options.advanced', $config['admin_extensions']['menu_options']['advanced']);

        $loader->load('admin-extension.xml');
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace()
    {
        return 'http://cmf.symfony.com/schema/dic/menu';
    }
}
