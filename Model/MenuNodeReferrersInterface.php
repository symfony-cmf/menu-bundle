<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Model;

use Knp\Menu\NodeInterface;

/**
 * Interface to be implemented by content that exposes editable menu referrers.
 * This is used with the Sonata MenuAwareExtension.
 */
interface MenuNodeReferrersInterface
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
