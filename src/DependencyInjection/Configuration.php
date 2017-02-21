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

use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;
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
                            ->canBeEnabled()
                            ->children()
                                ->scalarNode('menu_basepath')->defaultValue('/cms/menu')->end()
                                ->scalarNode('content_basepath')->defaultValue('/cms/content')->end()
                                ->integerNode('prefetch')->defaultValue(10)->end()
                                ->scalarNode('manager_name')->defaultNull()->end()
                                ->scalarNode('menu_document_class')->defaultValue(Menu::class)->end()
                                ->scalarNode('node_document_class')->defaultValue(MenuNode::class)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->scalarNode('content_url_generator')->defaultValue('router')->end()
                ->booleanNode('allow_empty_items')->defaultFalse()->end()

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

                ->arrayNode('publish_workflow')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('enabled')
                            ->values([true, false, 'auto'])
                            ->defaultValue('auto')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
