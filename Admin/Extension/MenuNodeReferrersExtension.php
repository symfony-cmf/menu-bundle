<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
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
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form.group_menus', array(
                'translation_domain' => 'CmfMenuBundle',
            ))
            ->add(
                'menuNodes',
                'sonata_type_collection',
                array(),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                ))
            ->end()
        ;
    }
}
