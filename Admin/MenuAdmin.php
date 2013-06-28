<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;
use Symfony\Cmf\Bundle\MenuBundle\Document\MenuNode;
use Symfony\Component\HttpFoundation\Request;

class MenuAdmin extends Admin
{
    protected $translationDomain = 'CmfMenuBundle';
    protected $contentRoot;
    protected $menuRoot;

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
            ->with('form.group_menu')
            ->add(
                'name',
                'text',
                ($this->hasSubject() && null !== $this->getSubject()->getId()) ? array('attr' => array('readonly' => 'readonly')) : array())
            ->add(
                'parent',
                'doctrine_phpcr_odm_tree',
                array(
                    'root_node' => $this->root,
                    'choice_list' => array(), 
                    'select_root_node' => true
                )
            )
            ->end()
        ;

        $formMapper->with('form.group_root')
            ->add('label', 'text')
            ->add('route', 'text', array('required' => false))
            ->add('uri', 'text', array('required' => false))
            ->add(
                'content',
                'doctrine_phpcr_odm_tree',
                array('root_node' => $this->contentRoot, 'choice_list' => array(), 'required' => false)
            )
            ->end()
        ;

        $subject = $this->getSubject();
        $isNew = $subject->getId() ? false : true;

        if (false === $isNew) {
            $formMapper
                ->with('form.group_items', array())
                ->add('children', 'doctrine_phpcr_odm_tree_manager', array(
                    'root' => $this->menuRoot,
                    'edit_in_overlay' => false,
                    'create_in_overlay' => false,
                ), array(
                    'help' => 'help.items_help'
                ))
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
}
