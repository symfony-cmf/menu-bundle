<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Cmf\Bundle\MenuBundle\DependencyInjection\Compiler\AddVotersPass;
use Doctrine\Bundle\PHPCRBundle\DependencyInjection\Compiler\DoctrinePhpcrMappingsPass;
use Symfony\Component\DependencyInjection\Definition;

class CmfMenuBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new AddVotersPass());

        $this->buildPhpcrCompilerPass($container);
        $this->buildOrmCompilerPass($container);
    }

    /**
     * Creates and registers compiler passes for ORM mappings if both doctrine
     * ORM and a suitable compiler pass implementation are available.
     *
     * @param ContainerBuilder $container
     */
    private function buildOrmCompilerPass(ContainerBuilder $container)
    {
        if (!class_exists('Doctrine\ORM\Version')) {
            return;
        }

        $doctrineOrmCompiler = $this->findDoctrineOrmCompiler();
        if (!$doctrineOrmCompiler) {
            return;
        }

        $container->addCompilerPass(
            $doctrineOrmCompiler::createXmlMappingDriver(
                array(
                    realpath(__DIR__ . '/Resources/config/doctrine-model') => 'Symfony\Cmf\Bundle\MenuBundle\Model',
                    realpath(__DIR__ . '/Resources/config/doctrine-orm') => 'Symfony\Cmf\Bundle\MenuBundle\Doctrine\Orm',
                ),
                array('cmf_menu.dynamic.persistence.orm.manager_name'),
                'cmf_menu.backend_type_orm'
            )
        );
    }

    /**
     * Creates and registers compiler passes for PHPCR-ODM mapping if both the
     * phpcr-odm and the phpcr-bundle are present.
     *
     * @param ContainerBuilder $container
     */
    private function buildPhpcrCompilerPass(ContainerBuilder $container)
    {
        if (!class_exists('Doctrine\Bundle\PHPCRBundle\DependencyInjection\Compiler\DoctrinePhpcrMappingsPass')
            || !class_exists('Doctrine\ODM\PHPCR\Version')
        ) {
            return;
        }

        $container->addCompilerPass(
            DoctrinePhpcrMappingsPass::createXmlMappingDriver(
                array(
                    realpath(__DIR__.'/Resources/config/doctrine-model') => 'Symfony\Cmf\Bundle\MenuBundle\Model',
                    realpath(__DIR__.'/Resources/config/doctrine-phpcr') => 'Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr',
                ),
                array('cmf_menu.dynamic.persistence.phpcr.manager_name'),
                'cmf_menu.backend_type_phpcr'
            )
        );
    }

    /**
     * Looks for a mapping compiler pass. If available, use the one from
     * DoctrineBundle (available only since DoctrineBundle 2.4 and Symfony 2.3)
     * Otherwise use the standalone one from CmfCoreBundle.
     *
     * @return boolean|string the compiler pass to use or false if no suitable
     *                        one was found
     */
    private function findDoctrineOrmCompiler()
    {
        if (class_exists('Symfony\Bridge\Doctrine\DependencyInjection\CompilerPass\RegisterMappingsPass')
            && class_exists('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')
        ) {
            return 'Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass';
        }

        if (class_exists('Symfony\Cmf\Bundle\CoreBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')) {
            return 'Symfony\Cmf\Bundle\CoreBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass';
        }

        return false;
    }
}
