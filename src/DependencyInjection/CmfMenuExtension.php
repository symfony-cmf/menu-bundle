<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\DependencyInjection;

use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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
        $container->setAlias('cmf_menu.content_router', $config['content_url_generator']);
        $container->setParameter($this->getAlias().'.allow_empty_items', $config['allow_empty_items']);

        $this->loadVoters($config, $loader, $container);

        if ($config['persistence']['phpcr']['enabled']) {
            $this->loadPhpcr($config['persistence']['phpcr'], $loader, $container);
        }

        if (true === $config['publish_workflow']['enabled']
            || ('auto' === $config['publish_workflow']['enabled'] && isset($bundles['CmfCoreBundle']))
        ) {
            $loader->load('publish-workflow.xml');
        }
    }

    public function loadVoters($config, XmlFileLoader $loader, ContainerBuilder $container)
    {
        $loader->load('voters.xml');

        if (isset($config['voters']['content_identity'])) {
            if (empty($config['voters']['content_identity']['content_key'])) {
                if (!class_exists(DynamicRouter::class)) {
                    throw new \RuntimeException('You need to set the content_key when not using the CmfRoutingBundle DynamicRouter');
                }
                $contentKey = DynamicRouter::CONTENT_KEY;
            } else {
                $contentKey = $config['voters']['content_identity']['content_key'];
            }
            $container->setParameter($this->getAlias().'.content_key', $contentKey);
        } else {
            $container->removeDefinition('cmf_menu.current_item_voter.content_identity');
        }

        if (isset($config['voters']) && !array_key_exists('uri_prefix', $config['voters'])) {
            $container->removeDefinition('cmf_menu.current_item_voter.uri_prefix');
        }
    }

    public function loadPhpcr($config, XmlFileLoader $loader, ContainerBuilder $container)
    {
        $keys = [
            'menu_document_class' => 'menu_document.class',
            'node_document_class' => 'node_document.class',
            'menu_basepath' => 'menu_basepath',
            'content_basepath' => 'content_basepath',
            'manager_name' => 'manager_name',
            'prefetch' => 'prefetch',
        ];

        foreach ($keys as $sourceKey => $targetKey) {
            $container->setParameter(
                $this->getAlias().'.persistence.phpcr.'.$targetKey,
                $config[$sourceKey]
            );
        }

        $loader->load('persistence-phpcr.xml');
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
