<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Admin\Extension;

use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Admin extension for editing menu options
 * implementing MenuOptionsInterface.
 *
 * @author Mojtaba Koosej <mkoosej@gmail.com>
 */
class MenuOptionsExtension extends AdminExtension
{
    /**
     * @var string
     */
    protected $formGroup;

    /**
     * @var string
     */
    protected $formTab;

    /**
    * @var bool
    */
    protected $advanced;

    /**
     * @param string $formGroup - group to use for form mapper
     * @param bool   $advanced - activates editing all fields of the node
     */
    public function __construct($formGroup = 'form.group_menu_options', $formTab = 'form.tab_menu', $advanced = false)
    {
        $this->formGroup = $formGroup;
        $this->formTab = $formTab;
        $this->advanced = $advanced;
    }

    /**
     * {@inheritDoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        if ($formMapper->hasOpenTab()) {
            $formMapper->end();
        }

        $formMapper
            ->tab($this->formTab, 'form.tab_menu' === $this->formTab
                ? array('translation_domain' => 'CmfMenuBundle')
                : array()
            )
                ->with($this->formGroup, 'form.group_menu_options' === $this->formGroup
                    ? array('translation_domain' => 'CmfMenuBundle')
                    : array()
                )
                    ->add(
                        'display',
                        'checkbox',
                        array('required' => false),
                        array('help' => 'form.help_display')
                    )
                    ->add(
                        'displayChildren',
                        'checkbox',
                        array('required' => false),
                        array('help' => 'form.help_display_children')
                    )
                ->end()
        ;

        if (!$this->advanced) {
            $formMapper->end();

            return;
        }

        $child_options = array(
            'value_type' => 'text',
            'label' => false,
            'attr'=> array('style' => 'clear:both')
        );

        $formMapper
            ->with($this->formGroup)
                ->add(
                    'attributes',
                    'burgov_key_value',
                    array(
                    'value_type' => 'text',
                    'required' => false,
                    'options' => $child_options,
                    )
                )
                ->add(
                    'labelAttributes',
                    'burgov_key_value',
                    array(
                    'value_type' => 'text',
                    'required' => false,
                    'options' => $child_options,
                    )
                )
                ->add(
                    'childrenAttributes',
                    'burgov_key_value',
                    array(
                    'value_type' => 'text',
                    'required' => false,
                    'options' => $child_options,
                    )
                )
                ->add(
                    'linkAttributes',
                    'burgov_key_value',
                    array(
                    'value_type' => 'text',
                    'required' => false,
                    'options' => $child_options,
                    )
                )
            ->end();
    }
}
