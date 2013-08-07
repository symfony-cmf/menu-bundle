<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Admin;

use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNode;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Doctrine\Common\Util\ClassUtils;

class MenuNodeAdmin extends AbstractMenuNodeAdmin
{
    protected $baseRouteName = 'cmf_menu_menunode';
    protected $baseRoutePattern = '/cmf/menu/menunode';

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
            ClassUtils::getClass($menuDoc)
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
                'ID for menu node "%s" may not be the same as root path "%s"',
                $id, $this->menuRoot
            ));
        }

        if (count($parts) == 1) {
            throw new \InvalidArgumentException(sprintf(
                'MenuNode "%s" may not hold the position reserved for a Menu.',
                $id
            ));
        }

        $first = $parts[0];
        $menuId = sprintf('%s/%s', $this->menuRoot, $first);

        return $menuId;
    }
}
