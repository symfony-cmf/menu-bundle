<?php

namespace Symfony\Cmf\Bundle\MenuBundle;

use Knp\Menu\NodeInterface;

/**
 * Interface to be implemented by content that exposes editable menu referrers.
 * This is used with the Sonata MenuAwareExtension.
 */
interface MenuAwareEditInterface extends MenuReferenceInterface
{
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
