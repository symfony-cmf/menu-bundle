<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Event;

use Knp\Menu\ItemInterface;
use Knp\Menu\NodeInterface;

use Symfony\Component\EventDispatcher\Event;

use Symfony\Cmf\Bundle\MenuBundle\ContentAwareFactory;

/**
 * This event is raised when a menu node is to be transformed into a menu item.
 *
 * The event allows to control whether the menu node should be handled or to
 * completely replace the default behaviour of converting a menu node to a menu
 * item.
 *
 * @author Ben Glassman <bglassman@gmail.com>
 */
class CreateMenuItemFromNodeEvent extends Event
{
    /**
     * @var NodeInterface
     */
    private $node;

    /**
     * @var ItemInterface
     */
    private $item;

    /**
     * @var ContentAwareFactory
     */
    private $factory;

    /**
     * Whether or not to skip processing of this node
     *
     * @var boolean
     */
    private $skipNode = false;

    /**
     * Whether or not to skip processing of child nodes
     *
     * @var boolean
     */
    private $skipChildren = false;

    /**
     * @param NodeInterface       $node
     * @param ContentAwareFactory $factory
     */
    public function __construct(
        NodeInterface $node,
        ContentAwareFactory $factory
    ) {
        $this->node = $node;
        $this->factory = $factory;
    }

    /**
     * Get the menu node that is about to be built.
     *
     * @return NodeInterface
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Get the menu item attached to this event.
     *
     * If this is non-null, it will be used instead of automatically converting
     * the NodeInterface into a MenuItem.
     *
     * @return ItemInterface
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set the menu item that represents the menu node of this event.
     *
     * Unless you set the skip children option, the children from the menu node
     * will still be built and added after eventual children this menu item
     * has.
     *
     * @param ItemInterface $item Menu item to use.
     */
    public function setItem(ItemInterface $item = null)
    {
        $this->item = $item;
    }

    /**
     * Set whether the node associated with this event is to be skipped
     * entirely. This has precedence over an eventual menu item attached to the
     * event.
     *
     * This automatically skips the whole subtree, as the children have no
     * place where they could be attached to.
     *
     * @param bool $skipNode
     */
    public function setSkipNode($skipNode)
    {
        $this->skipNode = (bool) $skipNode;
    }

    /**
     * @return bool Whether the node associated to this event is to be skipped.
     */
    public function isSkipNode()
    {
        return $this->skipNode;
    }

    /**
     * Set whether the children of the *node* associated with this event should
     * be ignored.
     *
     * Use this for example when your event handler implements its own logic to
     * build children items for the node associated with this event.
     *
     * If this event has a menu *item*, those children won't be skipped.
     *
     * @param bool $skipChildren
     */
    public function setSkipChildren($skipChildren)
    {
        $this->skipChildren = (bool) $skipChildren;
    }

    /**
     * @return bool Whether the children of the node associated to this event
     *              should be handled or ignored.
     */
    public function isSkipChildren()
    {
        return $this->skipChildren;
    }

    /**
     * Get the menu factory that raised this event.
     *
     * You can use the factory to build a custom menu item.
     *
     * @return ContentAwareFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }
}
