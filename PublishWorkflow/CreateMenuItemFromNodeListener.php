<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\PublishWorkflow;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Cmf\Bundle\MenuBundle\Event\CreateMenuItemFromNodeEvent;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowChecker;

/**
 * Listener for the CREATE_ITEM_FROM_NODE event that skips the node if it is
 * not published.
 *
 * @author Ben Glassman <bglassman@gmail.com>
 */
class CreateMenuItemFromNodeListener
{
    /**
     * @var SecurityContextInterface
     */
    private $publishWorkflowChecker;

    /**
     * The permission to check for when doing the publish workflow check.
     *
     * @var string
     */
    private $publishWorkflowPermission;

    /**
     * @param AuthorizationCheckerInterface $publishWorkflowChecker The publish workflow checker.
     * @param string                        $attribute              The permission to check.
     */
    public function __construct(AuthorizationCheckerInterface $publishWorkflowChecker, $attribute = PublishWorkflowChecker::VIEW_ATTRIBUTE)
    {
        $this->publishWorkflowChecker = $publishWorkflowChecker;
        $this->publishWorkflowPermission = $attribute;
    }

    /**
     * Check if the node on the event is published, otherwise skip it.
     *
     * @param CreateMenuItemFromNodeEvent $event
     */
    public function onCreateMenuItemFromNode(CreateMenuItemFromNodeEvent $event)
    {
        $node = $event->getNode();

        if (!$this->publishWorkflowChecker->isGranted($this->publishWorkflowPermission, $node)) {
            $event->setSkipNode(true);
        }
    }
}
