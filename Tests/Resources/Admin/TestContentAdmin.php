<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNode;
use Symfony\Component\HttpFoundation\Request;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Symfony\Cmf\Bundle\MenuBundle\ContentAwareFactory;
use Doctrine\Common\Util\ClassUtils;

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
