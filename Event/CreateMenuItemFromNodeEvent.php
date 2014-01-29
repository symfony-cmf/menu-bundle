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
    public function __construct(
        NodeInterface $node,
        MenuItem $item = null,
        ContentAwareFactory $factory
    ) {
        $this->node = $node;
        $this->item = $item;
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

}
