<?php

namespace Symfony\Cmf\Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder->root('symfony_cmf_menu')
            ->fixXmlConfig('voter')
            ->children()
                ->scalarNode('menu_basepath')->defaultValue('/cms/menu')->end()
                ->scalarNode('document_manager_name')->defaultValue('default')->end()
                ->scalarNode('admin_class')->defaultNull()->end()
                ->scalarNode('document_class')->defaultNull()->end()

                ->scalarNode('content_url_generator')->defaultValue('router')->end()

                ->scalarNode('content_basepath')->defaultNull()->end()

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

                ->enumNode('use_sonata_admin')
                    ->values(array(true, false, 'auto'))
                    ->defaultValue('auto')
                ->end()
                ->arrayNode('multilang')
                    ->children()
                        ->enumNode('use_sonata_admin')
                            ->values(array(true, false, 'auto'))
                            ->defaultValue('auto')
                        ->end()
                        ->scalarNode('admin_class')->defaultNull()->end()
                        ->scalarNode('document_class')->defaultNull()->end()
                        ->arrayNode('locales')
                            ->isRequired()
                            ->requiresAtLeastOneElement()
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
