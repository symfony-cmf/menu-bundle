<?php

namespace Symfony\Cmf\Component\Routing;

use Knp\Menu\NodeInterface;

/**
 * Interface to be implemented by content that exposes editable menu referrers.
 * This is used with the Sonata MenuAwareExtension.
 */
interface MenuAwareEditInterface
{
    /**
     * Get all menu nodes that point to this content.
     *
     * @return NodeInterface[] Menu nodes that point to this content
     */
    public function getMenus();

    /**
     * Add a menu node for this content.
     *
     * @param NodeInterface $menu
     */
    public function addMenu(NodeInterface $menu);

    /**
     * Remove a menu node for this content.
     *
     * @param NodeInterface $menu
     */
    public function removeMenu(NodeInterface $menu);
}
