<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrinePHPCRAdminBundle\Form\Type\TreeManagerType;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu;

class MenuAdmin extends AbstractMenuNodeAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);

        $subject = $this->getSubject();
        $isNew = $subject->getId() === null;

        if (!$isNew) {
            $formMapper
                ->with('form.group_items', array())
                    ->add('children', TreeManagerType::class, array(
                        'root' => $this->menuRoot,
                        'edit_in_overlay' => false,
                        'create_in_overlay' => false,
                        'delete_in_overlay' => false,
                    ), array(
                        'help' => 'help.items_help',
                    ))
                ->end()
            ;
        }
    }

    public function getNewInstance()
    {
        /** @var $new Menu */
        $new = parent::getNewInstance();
        $new->setParentDocument($this->getModelManager()->find(null, $this->menuRoot));

        return $new;
    }
}
