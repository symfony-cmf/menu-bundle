<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class MultilangMenuItemAdmin extends MenuItemAdmin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);
        $listMapper
            ->add('locales', 'choice', array('template' => 'SonataDoctrinePHPCRAdminBundle:CRUD:locales.html.twig'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('locale', 'text')
            ->end();

        parent::configureFormFields($formMapper);
    }
}
