<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Admin;

use Symfony\Cmf\Bundle\MenuBundle\Admin\MenuNodeAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class MenuAdmin extends MenuNodeCommon
{
    protected $baseRouteName = 'cmf_menu';
    protected $baseRoutePattern = '/cmf/menu/menu';

    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);

        $subject = $this->getSubject();
        $isNew = $subject->getId() ? false : true;

        if (false === $isNew) {
            $formMapper
                ->with('form.group_items', array())
                ->add('children', 'doctrine_phpcr_odm_tree_manager', array(
                    'root' => $this->menuRoot,
                    'edit_in_overlay' => false,
                    'create_in_overlay' => false,
                ), array(
                    'help' => 'help.items_help'
                ))
                ->end()
            ;
        }
    }
}
