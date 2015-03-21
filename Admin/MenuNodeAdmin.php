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

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;
use Symfony\Cmf\Bundle\MenuBundle\Model\Menu;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Doctrine\Common\Util\ClassUtils;

class MenuNodeAdmin extends AbstractMenuNodeAdmin
{
    protected $recursiveBreadcrumbs = true;

    protected function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);

        $listMapper
            ->add('uri', 'text')
            ->add('route', 'text')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form.group_general')
                ->add(
                    'parent',
                    'doctrine_phpcr_odm_tree',
                    array('root_node' => $this->menuRoot, 'choice_list' => array(), 'select_root_node' => true)
                )
            ->end()
        ;

        parent::configureFormFields($formMapper);

        if (null === $this->getParentFieldDescription()) {

            // Add the choice for the node links "target"
            $formMapper
                ->with('form.group_general')
                    ->add('linkType', 'choice_field_mask', array(
                        'map' => array(
                            'route' => array('route'),
                            'uri' => array('uri'),
                            'content' => array('content', 'doctrine_phpcr_odm_tree'),
                        ),
                        'empty_value' => 'auto',
                        'required' => false
                    ))
                    ->add('route', 'text', array('required' => false))
                    ->add('uri', 'text', array('required' => false))
                    ->add('content', 'doctrine_phpcr_odm_tree',
                        array(
                            'root_node' => $this->contentRoot,
                            'choice_list' => array(),
                            'required' => false
                        )
                    )
                ->end()
            ;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function buildBreadcrumbs($action, MenuItemInterface $menu = null)
    {
        $menuNodeNode = parent::buildBreadcrumbs($action, $menu);

        if ($action != 'edit' || ! $this->recursiveBreadcrumbs) {
            return $menuNodeNode;
        }

        $parentDoc = $this->getSubject()->getParentDocument();
        $pool = $this->getConfigurationPool();
        $parentAdmin = $pool->getAdminByClass(
            ClassUtils::getClass($parentDoc)
        );

        if (null === $parentAdmin) {
            return $menuNodeNode;
        }

        $parentAdmin->setSubject($parentDoc);
        $parentEditNode = $parentAdmin->buildBreadcrumbs($action, $menu);
        if ($parentAdmin->isGranted('EDIT' && $parentAdmin->hasRoute('edit'))) {
            $parentEditNode->setUri(
                $parentAdmin->generateUrl('edit', array(
                    'id' => $this->getUrlsafeIdentifier($parentDoc)
                ))
            );
        }

        $menuNodeNode->setParent(null);
        $current = $parentEditNode->addChild($menuNodeNode);

        return $current;
    }

    public function setRecursiveBreadcrumbs($recursiveBreadcrumbs)
    {
        $this->recursiveBreadcrumbs = (bool) $recursiveBreadcrumbs;
    }

}
