<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Event;

final class Events
{
    /**
     * Fired when a menu item is to be created from a node in ContentAwareFactory.
     *
     * The event object is a CreateMenuItemFromNodeEvent.
     */
    public const CREATE_ITEM_FROM_NODE = 'cmf_menu.create_menu_item_from_node';
}
