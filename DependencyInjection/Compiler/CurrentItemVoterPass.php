<?php

namespace Symfony\Cmf\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * A compiler pass to find current menu item voters and add them to the content
 * aware factory.
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
class CurrentItemVoterPass implements CompilerPassInterface
{
    /**
     * Adds any tagged current item voters to the content aware factory
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('symfony_cmf_menu.factory')) {
            return;
        }

        $router = $container->getDefinition('symfony_cmf_menu.factory');

        foreach ($container->findTaggedServiceIds('symfony_cmf_menu.current_item_voter') as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? (integer) $attributes[0]['priority'] : 0;
            $router->addMethodCall('addCurrentItemVoter', array(new Reference($id), $priority));
        }
    }
}
