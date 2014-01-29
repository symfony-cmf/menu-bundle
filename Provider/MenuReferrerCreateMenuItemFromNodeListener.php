<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Provider;

namespace Symfony\Cmf\Bundle\MenuBundle\Provider\MenuReferrerInterface;

use Knp\Menu\Provider\MenuProviderInterface;

// FIXME: This class needs testing
class MenuReferrerCreateMenuItemFromNodeListener
{
    protected $provider;

    public function __construct(MenuProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function onCreateMenuItemFromNode(CreateMenuItemFromNodeEvent $event)
    {
        $item = $event->getItem();
        $node = $event->getNode();
        $factory = $event->getFactory();

        if ( ! $node instanceof MenuReferrerInterface) {
            return;
        }

        if ( ! $node->hasMenu()) {
            return;
        }

        if ( ! $this->provider->has($node->getMenuName())) {
            return;
        }

        $name = $node->getMenuName();
        $options = $node->getMenuOptions();

        $menu = $this->provider->get($name, $options);

        $position = isset($options['position']) ? $options['position'] : -1;

        $oldChildren = $item->getChildren();
        $newChildren = $menu->getChildren();
        $newChildren = array_walk($newChildren, function ($child) { 
            $child->setParent(null);
        });

        if ($position == 0) {
            $children = array_merge($newChildren, $oldChildren);
        } else if ($position == -1) {
            $children = array_merge($oldChildren, $newChildren);
        } else {
            $children = $oldChildren();
            array_splice($children, $position, 0, $newChildren);
        }

        $item->setChildren($children);

        $event->setItem($item);
    }
}
