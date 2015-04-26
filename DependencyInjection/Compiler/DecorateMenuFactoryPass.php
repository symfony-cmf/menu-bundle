<?php

namespace Symfony\Cmf\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class DecorateMenuFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @todo Add `decorates="knp_menu.factory"` to the service definition
     *       instead if Symfony 2.3 support is dropped.
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('knp_menu.factory')) {
            return;
        }

        $knpFactory = $container->getDefinition('knp_menu.factory');
        $knpFactory->setPublic(false);

        // rename old service
        $container->setDefinition('cmf_menu.factory.quiet.inner', $knpFactory);

        $container->setAlias('knp_menu.factory', new Alias('cmf_menu.factory.quiet'));
    }
}
