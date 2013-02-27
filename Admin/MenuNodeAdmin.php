<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;

class MenuNodeAdmin extends MinimalMenuAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add(
                'content',
                'doctrine_phpcr_odm_tree',
                array('root_node' => $this->contentRoot, 'choice_list' => array(), 'required' => false)
            );
    }

}
