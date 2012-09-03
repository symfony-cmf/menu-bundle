<?php

namespace Symfony\Cmf\Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('symfony_cmf_menu');

        $rootNode
            ->children()
                ->scalarNode('menu_basepath')->defaultValue('/cms/menu')->end()
                ->scalarNode('document_manager_name')->defaultValue('default')->end()
                ->scalarNode('menu_document_class')->defaultNull()->end()

                ->scalarNode('content_url_generator')->defaultValue('router')->end()
                ->scalarNode('content_key')->defaultNull()->end()
                ->scalarNode('route_name')->defaultNull()->end()

                ->scalarNode('use_sonata_admin')->defaultValue('auto')->end()
                ->scalarNode('use_sonata_admin')
                    ->defaultValue('auto')
                    ->validate()
                        ->ifTrue(function($v){
                            return !is_bool($v) && $v !== 'auto';
                        })
                        ->thenInvalid("This configuration allows only the values true, false or 'auto'")
                    ->end()
                ->end()
                ->scalarNode('content_basepath')->defaultNull()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
