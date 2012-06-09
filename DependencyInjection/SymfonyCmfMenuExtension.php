<?php
namespace Symfony\Cmf\Bundle\MenuBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

use Symfony\Cmf\Bundle\RoutingExtraBundle\Routing\DynamicRouter;

class SymfonyCmfMenuExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('phpcr-menu.xml');

        if ($config['use_sonata_admin']) {
            $loader->load('menu-admin.xml');
            $contentBasepath = $config['content_basepath'];
            if (null === $contentBasepath) {
                if ($container->hasParameter('symfony_cmf_core.content_basepath')) {
                    $contentBasepath = $container->getParameter('symfony_cmf_core.content_basepath');
                } else {
                    $contentBasepath = '/cms/content';
                }
            }
            $container->setParameter($this->getAlias() . '.content_basepath', $contentBasepath);
        }

        $container->setParameter($this->getAlias() . '.menu_basepath', $config['menu_basepath']);
        $container->setParameter($this->getAlias() . '.document_manager_name', $config['document_manager_name']);
        $container->setParameter($this->getAlias() . '.menu_document_class', $config['menu_document_class']);

        $factory = $container->getDefinition($this->getAlias().'.factory');
        $factory->replaceArgument(2, new Reference($config['content_url_generator']));
        $container->setParameter($this->getAlias() . '.content_key', $config['content_key']);
        if (empty($config['content_key'])) {
            if (! class_exists('Symfony\\Cmf\\Bundle\\RoutingExtraBundle\\Routing\\DynamicRouter')) {
                throw new \RuntimeException('You need to set the content_key when not using the SymfonyCmfRoutingExtraBundle DynamicRouter');
            }
            $config['content_key'] = DynamicRouter::CONTENT_KEY;
        }
        $container->setParameter($this->getAlias() . '.content_key', $config['content_key']);
        $container->setParameter($this->getAlias() . '.route_name', $config['route_name']);
    }
}
