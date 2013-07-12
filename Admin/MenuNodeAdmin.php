<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;
use Symfony\Cmf\Bundle\MenuBundle\Document\MenuNode;
use Symfony\Component\HttpFoundation\Request;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Symfony\Cmf\Bundle\MenuBundle\ContentAwareFactory;

class MenuNodeAdmin extends MenuAdmin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', 'text')
            ->add('name', 'text')
            ->add('label', 'text')
            ->add('uri', 'text')
            ->add('route', 'text')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form.group_general')
            ->add(
                'parent',
                'doctrine_phpcr_odm_tree',
                array('root_node' => $this->menuRoot, 'choice_list' => array(), 'select_root_node' => true)
            )
            ->add(
                'name',
                'text',
                ($this->hasSubject() && null !== $this->getSubject()->getId()) ? array('attr' => array('readonly' => 'readonly')) : array())
            ->add('label', 'text')
            ->end()
        ;

        if (null === $this->getParentFieldDescription()) {
            $formMapper
                ->with('form.group_target', array(
                    'template' => 'CmfMenuBundle:Admin:menu_node_target_group.html.twig',
                ))
                ->add('linkType', 'choice_field_mask', array(
                    'choices' => array_combine(
                        $this->contentAwareFactory->getLinkTypes(),
                        $this->contentAwareFactory->getLinkTypes()
                    ),
                    'map' => array(
                        'route' => array('route'),
                        'uri' => array('uri'),
                        'content' => array('content', 'doctrine_phpcr_odm_tree', 'weak'),
                    ),
                    'empty_value' => 'auto',
                ))
                ->add('weak', 'checkbox', array('required' => false))
                ->add('route', 'text', array('required' => false))
                ->add('uri', 'text', array('required' => false))
                ->add(
                    'content',
                    'doctrine_phpcr_odm_tree',
                    array('root_node' => $this->contentRoot, 'choice_list' => array(), 'required' => false)
                )
                ->end()
            ;
        }
    }

    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id', 'text')
            ->add('name', 'text')
            ->add('label', 'text')
            ->add('uri', 'text')
            ->add('content', 'text')
        ;
    }

    /**
     * @return MenuNode
     */
    public function getNewInstance()
    {
        /** @var $new MenuNode */
        $new = parent::getNewInstance();
        if ($this->hasRequest()) {
            $parentId = $this->getRequest()->query->get('parent');
            if (null !== $parentId) {
                $new->setParent($this->getModelManager()->find(null, $parentId));
            }
        }
        return $new;
    }

    public function getExportFormats()
    {
        return array();
    }

    public function setContentRoot($contentRoot)
    {
        $this->contentRoot = $contentRoot;
    }

    public function setMenuRoot($menuRoot)
    {
        $this->menuRoot = $menuRoot;
    }

    public function setContentTreeBlock($contentTreeBlock)
    {
        $this->contentTreeBlock = $contentTreeBlock;
    }

    /**
     * Return the content tree to show at the left, current node (or parent for new ones) selected
     *
     * @param string $position
     *
     * @return array
     */
    public function getBlocks($position)
    {
        if ('left' == $position) {
            $selected = ($this->hasSubject() && $this->getSubject()->getId()) ? $this->getSubject()->getId(
            ) : ($this->hasRequest() ? $this->getRequest()->query->get('parent') : null);
            return array(
                array(
                    'type' => 'sonata_admin_doctrine_phpcr.tree_block',
                    'settings' => array('selected' => $selected)
                )
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function buildBreadcrumbs($action, MenuItemInterface $menu = null)
    {
        $menuNodeNode = parent::buildBreadcrumbs($action, $menu);

        if ($action != 'edit') {
            return $menuNodeNode;
        }

        $menuDoc = $this->getMenuForSubject($this->getSubject());
        $pool = $this->getConfigurationPool();
        $menuAdmin = $pool->getAdminByClass(
            $class = get_class($this->getSubject())
        );
        $menuAdmin->setSubject($menuDoc);
        $menuEditNode = $menuAdmin->buildBreadcrumbs($action, $menu);
        if ($menuAdmin->isGranted('EDIT' && $menuAdmin->hasRoute('edit'))) {
            $menuEditNode->setUri(
                $menuAdmin->generateUrl('edit', array(
                    'id' => $this->getUrlsafeIdentifier($menuDoc)
                ))
            );
        }

        $menuNodeNode->setParent(null);
        $current = $menuEditNode->addChild($menuNodeNode);

        return $current;
    }

    protected function getMenuForSubject(MenuNode $subject)
    {
        $id = $subject->getId();

        $menuId = $this->getMenuIdForNodeId($id);

        $menu = $this->modelManager->find(null, $menuId);

        return $menu;
    }

    protected function getMenuIdForNodeId($id)
    {
        // I wonder if this could be simplified in Phpcr/PathHelper
        //
        // $relPath = PathHelper::removeBasePath($this->menuRoot, $id);
        // $menuId = PathHelper:splicePath($relPath, 0, 1);

        if (0 !== strpos($id, $this->menuRoot)) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot find base path "%s" in menu node ID "%s"', $this->menuRoot, $id
            ));
        }

        $relPath = substr($id, strlen($this->menuRoot) + 1);
        $parts = explode('/', $relPath);

        if (count($parts) == 0) {
            throw new \InvalidArgumentException(sprintf(
                'ID for menu node "%s" is the same as root path "%s" - this is strange.',
                $id, $rootPath
            ));
        }

        if (count($parts) == 1) {
            throw new \InvalidArgumentException(sprintf(
                'MenuNode "%s" seems to hold the position reserved for a Menu. This should not happen',
                $id
            ));
        }

        $first = $parts[0];
        $menuId = sprintf('%s/%s', $this->menuRoot, $first);

        return $menuId;
    }
}
