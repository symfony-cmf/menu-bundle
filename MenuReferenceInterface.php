<?php

namespace Symfony\Cmf\Bundle\MenuBundle;

use Knp\Menu\NodeInterface;

/**
 * Interface to be implemented by content that knows about the menu items
 * referring to it.
 */
interface MenuReferenceInterface
{
    /**
     * Get all menu nodes that point to this content.
     *
     * @return NodeInterface[] Menu nodes that point to this content
     */
    public function getMenus();
}
