<?php

namespace Symfony\Cmf\Bundle\MenuBundle\PublishWorkflow;

use Symfony\Cmf\Bundle\MenuBundle\Event\CreateMenuItemFromNodeEvent;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowChecker;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Cmf\Bundle\MenuBundle\Voter\VoterInterface;

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

        // FIXME: If this is called for the root menu you get the following exception
        // Menu at 'menu-name' is misconfigured (f.e. the route might be incorrect) and could therefore not be instanciated
        // One way to avoid this possibility is to check if $node->getContent is empty. If its empty then publish workflow checker is irrelevant and we can avoid this call altogether but the event is type hinted on NodeInterface not MenuNode so we cant guarantee it has that method. We could type hint it on MenuNode or perhaps this is a non issue if the workflow permissions checker is set up to return true if the voter abstains
        if (!$this->securityContext->isGranted($this->publishWorkflowPermission, $node)) {
            $event->setItem(null);
        }

    }
}

