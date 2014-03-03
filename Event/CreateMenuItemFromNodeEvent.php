<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Event;

use Knp\Menu\MenuItem;
use Knp\Menu\NodeInterface;

use Symfony\Component\EventDispatcher\Event;

use Symfony\Cmf\Bundle\MenuBundle\ContentAwareFactory;

class CreateMenuItemFromNodeEvent extends Event
{
    /**
     * @var NodeInterface
     */
    protected $node;

    /**
     * @var ContentAwareFactory
     */
    protected $factory;

    /**
     * @var MenuItem
     */
    protected $item;

    /**
     * Whether or not to skip processing of this node
     * 
     * @var boolean
     */
    protected $skipNode = false;

    /**
     * Whether or not to skip processing of child nodes
     * 
     * @var boolean
     */
    protected $skipChildren = false;

    public function __construct(
        NodeInterface $node,
        ContentAwareFactory $factory
    ) {
        $this->node = $node;
        $this->factory = $factory;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function setItem(MenuItem $item = null)
    {
        $this->item = $item;
    }

    public function getFactory()
    {
        return $this->factory;
    }

    public function getNode()
    {
        return $this->node;
    }

    public function setSkipNode($skipNode)
    {
        $this->skipNode = (bool) $skipNode;
    }

    public function getSkipNode()
    {
        return $this->skipNode;
    }

    public function setSkipChildren($skipChildren)
    {
        $this->skipChildren = (bool) $skipChildren;
    }

    public function getSkipChildren()
    {
        return $this->skipChildren;
    }

}
