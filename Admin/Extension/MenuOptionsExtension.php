<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Admin\Extension;

use Burgov\Bundle\KeyValueFormBundle\Form\Type\KeyValueType;
use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
     * @var bool
     */
    protected $advanced;

    /**
     * @param string $formGroup - group to use for form mapper
     * @param bool   $advanced  - activates editing all fields of the node
     */
    public function __construct($formGroup = 'form.group_menu_options', $advanced = false)
    {
        $this->formGroup = $formGroup;
        $this->advanced = $advanced;
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->with($this->formGroup, array(
                'translation_domain' => 'CmfMenuBundle',
            ))
            ->add(
                'display',
                CheckboxType::class,
                array('required' => false),
                array('help' => 'form.help_display')
            )
            ->add(
                'displayChildren',
                CheckboxType::class,
                array('required' => false),
                array('help' => 'form.help_display_children')
            )
          ->end();

        if (!$this->advanced) {
            return;
        }

        $child_options = array(
            'value_type' => TextType::class,
            'label' => false,
            'attr' => array('style' => 'clear:both'),
        );

        $formMapper->with($this->formGroup, array(
                'translation_domain' => 'CmfMenuBundle',
            ))
            ->add(
                'attributes',
                KeyValueType::class,
                array(
                  'value_type' => TextType::class,
                  'required' => false,
                  'entry_options' => $child_options,
                )
            )
            ->add(
                'labelAttributes',
                KeyValueType::class,
                array(
                  'value_type' => TextType::class,
                  'required' => false,
                  'entry_options' => $child_options,
                )
            )
            ->add(
                'childrenAttributes',
                KeyValueType::class,
                array(
                  'value_type' => TextType::class,
                  'required' => false,
                  'entry_options' => $child_options,
                )
            )
            ->add(
                'linkAttributes',
                KeyValueType::class,
                array(
                  'value_type' => TextType::class,
                  'required' => false,
                  'entry_options' => $child_options,
                )
            )
          ->end();
    }
}
