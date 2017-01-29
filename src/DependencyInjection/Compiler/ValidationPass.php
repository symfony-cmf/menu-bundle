<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Register validation files only if their persistence layer is enabled.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class ValidationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter('cmf_menu.persistence.phpcr.menu_document_class')) {
            $container
                ->getDefinition('validator.builder')
                ->addMethodCall('addXmlMappings', [[__DIR__.'/../../Resources/config/validation-phpcr.xml']]);
        }
    }
}
