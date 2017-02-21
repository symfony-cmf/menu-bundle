<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle;

use Doctrine\Bundle\PHPCRBundle\DependencyInjection\Compiler\DoctrinePhpcrMappingsPass;
use Symfony\Cmf\Bundle\MenuBundle\DependencyInjection\Compiler\DecorateMenuFactoryPass;
use Symfony\Cmf\Bundle\MenuBundle\DependencyInjection\Compiler\ValidationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CmfMenuBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DecorateMenuFactoryPass());
        $container->addCompilerPass(new ValidationPass());

        if (class_exists(DoctrinePhpcrMappingsPass::class)) {
            $container->addCompilerPass(
                DoctrinePhpcrMappingsPass::createXmlMappingDriver(
                    [
                        realpath(__DIR__.'/Resources/config/doctrine-model') => 'Symfony\Cmf\Bundle\MenuBundle\Model',
                        realpath(__DIR__.'/Resources/config/doctrine-phpcr') => 'Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr',
                    ],
                    ['cmf_menu.manager_name'],
                    false,
                    ['CmfMenuBundle' => 'Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr']
                )
            );
        }
    }
}
