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

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNode;
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
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form.group_general')
                ->add('parent', 'doctrine_phpcr_odm_tree', array(
                    'root_node' => $this->menuRoot,
                    'choice_list' => array(),
                    'select_root_node' => true,
                ))
            ->end()
        ;

        parent::configureFormFields($formMapper);

        if (null === $this->getParentFieldDescription()) {
            // Add the choice for the node links "target"
            $formMapper
                ->with('form.group_general')
                    ->add('linkType', 'choice_field_mask', array(
                        'choices' => array(
                            'route' => 'route',
                            'uri' => 'uri',
                            'content' => 'content',
                        ),
                        'map' => array(
                            'route' => array('link'),
                            'uri' => array('link'),
                            'content' => array('content', 'doctrine_phpcr_odm_tree'),
                        ),
                        'empty_value' => 'auto',
                        'required' => false,
                    ))
                    ->add('link', 'text', array('required' => false, 'mapped' => false))
                    ->add('content', 'doctrine_phpcr_odm_tree',
                        array(
                            'root_node' => $this->contentRoot,
                            'choice_list' => array(),
                            'required' => false,
                        )
                    )
                ->end()
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFormBuilder()
    {
        $formBuilder = parent::getFormBuilder();

        $formBuilder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            if (!$event->getForm()->has('link')) {
                return;
            }

            $link = $event->getForm()->get('link');
            $node = $event->getData();

            if (!$node instanceof MenuNode) {
                return;
            }

            switch ($node->getLinkType()) {
                case 'route':
                    $link->setData($node->getRoute());
                    break;

                case 'uri':
                    $link->setData($node->getUri());
                    break;

                case null:
                    $linkType = $event->getForm()->get('linkType');

                    if ($data = $node->getUri()) {
                        $linkType->setData('uri');
                    } elseif ($data = $node->getRoute()) {
                        $linkType->setData('route');
                    } elseif ($node->getContent()) {
                        $linkType->setData('content');
                    } else {
                        $linkType->setData('auto');
                    }

                    $link->setData($data);
            }
        });

        $formBuilder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            if (!$event->getForm()->has('link')) {
                return;
            }

            $form = $event->getForm();
            $node = $event->getData();

            if (!$node instanceof MenuNode) {
                return;
            }

            $linkType = $form->get('linkType')->getData();
            $link = $form->get('link')->getData();

            switch ($linkType) {
                case 'route':
                    $node->setRoute($link);
                    break;

                case 'uri':
                    $node->setUri($link);
                    break;
            }
        });

        return $formBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBreadcrumbs($action, MenuItemInterface $menu = null)
    {
        $menuNodeItem = parent::buildBreadcrumbs($action, $menu);

        if ($action != 'edit' || !$this->recursiveBreadcrumbs) {
            return $menuNodeItem;
        }

        $parentDoc = $this->getSubject()->getParentDocument();
        $pool = $this->getConfigurationPool();
        $parentAdmin = $pool->getAdminByClass(
            ClassUtils::getClass($parentDoc)
        );

        if (null === $parentAdmin) {
            return $menuNodeItem;
        }

        $parentAdmin->setSubject($parentDoc);
        $parentAdmin->setRequest($this->request);
        $parentEditNode = $parentAdmin->buildBreadcrumbs($action, $menu);
        if ($parentAdmin->isGranted('EDIT' && $parentAdmin->hasRoute('edit'))) {
            $parentEditNode->setUri(
                $parentAdmin->generateUrl('edit', array(
                    'id' => $this->getUrlsafeIdentifier($parentDoc),
                ))
            );
        }

        $menuNodeItem->setParent(null);
        $current = $parentEditNode->addChild($menuNodeItem);

        return $current;
    }

    public function setRecursiveBreadcrumbs($recursiveBreadcrumbs)
    {
        $this->recursiveBreadcrumbs = (bool) $recursiveBreadcrumbs;
    }
}
