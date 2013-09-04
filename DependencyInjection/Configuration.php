<?php

namespace Symfony\Cmf\Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder->root('cmf_menu')
            ->fixXmlConfig('voter')
            ->children()
                ->arrayNode('persistence')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('phpcr')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('enabled')->defaultNull()->end()
                                ->scalarNode('menu_basepath')->defaultValue('/cms/menu')->end()
                                ->scalarNode('content_basepath')->defaultValue('/cms/content')->end()
                                ->scalarNode('manager_name')->defaultNull()->end()
                                ->scalarNode('menu_document_class')->defaultNull()->end()
                                ->scalarNode('node_document_class')->defaultNull()->end()

                                ->enumNode('use_sonata_admin')
                                    ->values(array(true, false, 'auto'))
                                    ->defaultValue('auto')
                                ->end()
                                ->scalarNode('menu_admin_class')->defaultNull()->end()
                                ->scalarNode('node_admin_class')->defaultNull()->end()
                                ->scalarNode('content_basepath')->defaultNull()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->scalarNode('content_url_generator')->defaultValue('router')->end()
                ->scalarNode('allow_empty_items')->defaultValue(false)->end()

                ->arrayNode('voters')
                    ->children()
                        ->arrayNode('content_identity')
                            ->children()
                                ->scalarNode('content_key')->defaultNull()->end()
                            ->end()
                        ->end()
                        ->scalarNode('uri_prefix')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
