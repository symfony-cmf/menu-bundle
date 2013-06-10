<?php
namespace Symfony\Cmf\Bundle\MenuBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
// this use is only used if the class really is present, no hard dependency
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;

class CmfMenuExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('phpcr-menu.xml');

        $this->loadVoters($config, $container);

        if ($config['use_sonata_admin']) {
            $this->loadSonataAdmin($config, $loader, $container);
        }

        if (isset($config['multilang'])) {
            if ($config['multilang']['use_sonata_admin']) {
                $this->loadSonataAdmin($config['multilang'], $loader, $container, 'multilang.');
            }
            if (isset($config['multilang']['document_class'])) {
                $container->setParameter($this->getAlias() . '.multilang.document_class', $config['multilang']['document_class']);
            }

            $container->setParameter($this->getAlias() . '.multilang.locales', $config['multilang']['locales']);
        }

        if (isset($config['document_class'])) {
            $container->setParameter($this->getAlias() . '.document_class', $config['document_class']);
        }

        $container->setParameter($this->getAlias() . '.menu_basepath', $config['menu_basepath']);
        $container->setParameter($this->getAlias() . '.document_manager_name', $config['document_manager_name']);
        $container->setParameter($this->getAlias() . '.allow_empty_items', $config['allow_empty_items']);

        $factory = $container->getDefinition($this->getAlias().'.factory');
        $factory->replaceArgument(1, new Reference($config['content_url_generator']));

        $contentBasepath = $config['content_basepath'];
        if (null === $contentBasepath) {
            if ($container->hasParameter('cmf_core.content_basepath')) {
                $contentBasepath = $container->getParameter('cmf_core.content_basepath');
            } else {
                $contentBasepath = '/cms/content';
            }
        }
        $container->setParameter($this->getAlias() . '.content_basepath', $contentBasepath);
    }

    public function loadVoters($config, ContainerBuilder $container)
    {
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

        if (! isset($config['voters']['uri_prefix'])) {
            $container->removeDefinition('cmf_menu.current_item_voter.uri_prefix');
        }
    }

    public function loadSonataAdmin($config, XmlFileLoader $loader, ContainerBuilder $container, $prefix = '')
    {
        $bundles = $container->getParameter('kernel.bundles');
        if ('auto' === $config['use_sonata_admin'] && !isset($bundles['SonataDoctrinePHPCRAdminBundle'])) {
            return;
        }

        if (isset($config['admin_class'])) {
            $container->setParameter($this->getAlias() . $prefix. '.admin_class', $config['admin_class']);
        }

        $loader->load($prefix.'admin.xml');
    }
}
