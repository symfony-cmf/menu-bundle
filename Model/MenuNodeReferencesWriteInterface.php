<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Model;

use Knp\Menu\NodeInterface;

/**
 * Interface to be implemented by content that exposes editable menu referrers.
 * This is used with the Sonata MenuAwareExtension.
 *
 * ###### Should we rather have just getMenuNodes and setMenuNodes - how will Sonata
 *        handle addMenuNode and removeMenuNode?
 */
interface MenuNodeReferenceManyWriteInterface
{
    /**
     * Get all menu nodes that point to this content.
     *
     * @return NodeInterface[] Menu nodes that point to this content
     */
    public function getMenuNodes();

    /**
     * Add a menu node for this content.
     *
     * @param NodeInterface $menu
     */
    public function addMenuNode(NodeInterface $menu);

    /**
     * Remove a menu node for this content.
     *
     * @param NodeInterface $menu
     */
    public function removeMenuNode(NodeInterface $menu);
}
