<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Voter;

use Knp\Menu\ItemInterface;

/**
 * Interface to implement for voters that decide if a menu item is the current
 * item.
 *
 * The voting process happens in ContentAwareFactory and continues as long
 * as voter return null. The first true or false is taken for the decision.
 * If all abstain, the menu item is not the current item.
 *
 * The menu bundle automatically registers all voters that are tagged with
 * cmf_menu.voter
 *
 * NOTE: KnpMenu 2.0 will have a voting mechanism. This is just a stop-gap
 * for the cmf until the point where we can switch to 2.0. This interface
 * and the semantic are the same already to make upgrade smooth.
 *
 * @see ContentAwareFactory
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
interface VoterInterface
{
    /**
     * @param ItemInterface $item
     *
     * @return boolean|null
     */
    public function matchItem(ItemInterface $item);
}
