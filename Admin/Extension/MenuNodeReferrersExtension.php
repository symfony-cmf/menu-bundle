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
 * Admin extension to add menu items tab to content.
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
class MenuNodeReferrersExtension extends AdminExtension
{
    /**
     * @var string
     */
    private $formTab;

    /**
     * @var string
     */
    private $formGroup;

    public function __construct($formGroup = 'form.group_menu', $formTab = 'form.tab_menu')
    {
        $this->formGroup = $formGroup;
        $this->formTab = $formTab;
    }

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
                ->with($this->formGroup, 'form.group_menu' === $this->formGroup
                    ? array('translation_domain' => 'CmfMenuBundle')
                    : array()
                )
                    ->add(
                        'menuNodes',
                        'sonata_type_collection',
                        array(),
                        array(
                            'edit' => 'inline',
                            'inline' => 'table',
                        )
                    )
                ->end()
            ->end()
        ;
    }
}
