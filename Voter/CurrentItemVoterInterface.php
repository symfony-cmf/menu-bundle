<?php
namespace Symfony\Cmf\Bundle\MenuBundle\Voter;

use Knp\Menu\NodeInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface to implement for voters that decide if a menu item being created
 * is the current item.
 *
 * The voting process happens in ContentAwareFactory and continues as long
 * as voter return VOTE_ABSTAIN. The first YES or NO is taken for the decision.
 * If all abstain, the menu item is not the current item.
 *
 * The menu bundle automatically registers all voters that are tagged with
 * symfony_cmf_menu.current_item_voter
 *
 * @see ContentAwareFactory
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
interface CurrentItemVoterInterface
{
    /**
     * This voter can not decide if this is the current item, voting should
     * continue.
     */
    const VOTE_ABSTAIN = 0;
    /**
     * This is the current item, stop voting.
     */
    const VOTE_YES     = 1;
    /**
     * This can not be the current item, stop voting.
     */
    const VOTE_NO      = 2;

    /**
     * @param Request       $request The current request
     * @param array         $options The options used to build the menu item
     * @param NodeInterface $node    The menu node this item is being built from, if any
     *
     * @return int One of the constants
     */
    public function isCurrentItem(Request $request, array $options, NodeInterface $node = null);
}
