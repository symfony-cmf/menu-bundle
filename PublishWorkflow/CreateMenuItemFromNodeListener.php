<?php

namespace Symfony\Cmf\Bundle\MenuBundle\PublishWorkflow;

use Symfony\Cmf\Bundle\MenuBundle\Event\CreateMenuItemFromNodeEvent;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowChecker;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Cmf\Bundle\MenuBundle\Voter\VoterInterface;
use Symfony\Cmf\Bundle\MenuBundle\Model\Menu;

class CreateMenuItemFromNodeListener
{
    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * The permission to check for when doing the publish workflow check.
     *
     * @var string
     */
    private $publishWorkflowPermission = PublishWorkflowChecker::VIEW_ATTRIBUTE;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function onCreateMenuItemFromNode(CreateMenuItemFromNodeEvent $event)
    {
        $node = $event->getNode();
        $item = $event->getItem();
        $factory = $event->getFactory();

        if ($node instanceof Menu) {
            return;
        }

        if (!$this->securityContext->isGranted($this->publishWorkflowPermission, $node)) {
            $event->setSkipNode(true);
        }

    }
}

