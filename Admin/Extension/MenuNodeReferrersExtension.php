<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Admin\Extension;

use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Admin extension to add menu items tab to content.
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
class MenuNodeReferrersExtension extends AdminExtension
{
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form.group_menus', array(
                'translation_domain' => 'CmfMenuBundle',
            ))
            ->add(
                'menuNodes',
                'sonata_type_collection',
                array(
                    'by_reference' => false,
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                ))
            ->end()
        ;
    }
}
