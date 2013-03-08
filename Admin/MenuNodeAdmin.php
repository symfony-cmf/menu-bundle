<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;

class MenuNodeAdmin extends MinimalMenuAdmin
{

    /**
     * Those two properties are needed to make it possible
     * to have 2 Admin classes for the same Document / Entity
     */
    protected $baseRouteName = 'admin_bundle_menu_menunode_list';
    protected $baseRoutePattern = 'bundle/menu/menunode';

    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);

        $formMapper
            ->with('form.group_general')
                ->add('route', 'text')
                ->add(
                    'content',
                    'doctrine_phpcr_odm_tree',
                    array('root_node' => $this->contentRoot, 'choice_list' => array(), 'required' => false)
                )
            ->end();
    }

}

