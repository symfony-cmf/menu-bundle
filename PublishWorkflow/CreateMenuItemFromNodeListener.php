<?php

namespace Symfony\Cmf\Bundle\MenuBundle\PublishWorkflow;

use Symfony\Component\Security\Core\SecurityContextInterface;
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
     * @param SecurityContextInterface $publishWorkflowChecker The publish workflow checker.
     * @param string                   $attribute              The permission to check.
     */
    public function __construct(SecurityContextInterface $publishWorkflowChecker, $attribute = PublishWorkflowChecker::VIEW_ATTRIBUTE)
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
