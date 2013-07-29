<?php

namespace Symfony\Cmf\Bundle\CoreBundle\Admin\Extension;

use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Admin extension to add menu items tab to content.
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
class MenuAwareExtension extends AdminExtension
{
    /**
     * @var array
     */
    protected $locales;

    /**
     * @param array  $locales
     */
    public function __construct($locales)
    {
        $this->locales = $locales;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form.group_menus', array(
                'translation_domain' => 'CmfMenuBundle',
            ))
            ->add(
                'menus',
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
