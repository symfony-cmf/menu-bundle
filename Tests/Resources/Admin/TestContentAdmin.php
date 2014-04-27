<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNode;

/**
 * Common base admin for Menu and MenuNode
 */
class TestContentAdmin extends Admin
{
    protected $baseRouteName = 'cmf_menu_test_content';
    protected $baseRoutePattern = '/cmf/menu-test/content';

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', 'text')
            ->add('title', 'text')
        ;

        $listMapper
            ->add('locales', 'choice', array(
                'template' => 'SonataDoctrinePHPCRAdminBundle:CRUD:locales.html.twig'
            ))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form.group_general')
                ->add('title', 'text')
            ->end()
        ;
    }
}
