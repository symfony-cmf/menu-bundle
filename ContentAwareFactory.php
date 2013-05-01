<?php

namespace Symfony\Cmf\Bundle\MenuBundle;

use Knp\Menu\Silex\RouterAwareFactory;
use Knp\Menu\MenuItem;
use Knp\Menu\NodeInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Psr\Log\LoggerInterface;

use Symfony\Cmf\Bundle\MenuBundle\Voter\CurrentItemVoterInterface;

/**
 * This factory builds menu items from the menu nodes and builds urls based on
 * the content these menu nodes stand for.
 *
 * The createItem method uses a voting process to decide whether the menu item
 * is the current item.
 */
class ContentAwareFactory extends RouterAwareFactory
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $contentRouter;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * List of priority => array of CurrentItemVoterInterface
     *
     * @var array
     */
    private $voters;

    /**
     * Sorted list of current item voters
     *
     * @var CurrentItemVoterInterface[]
     */
    private $sortedVoters;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ContainerInterface $container to fetch the request in order to determine
     *      whether this is the current menu item
     * @param UrlGeneratorInterface $generator for the parent class
     * @param UrlGeneratorInterface $contentRouter to generate routes when
     *      content is set
     */
    public function __construct(ContainerInterface $container, UrlGeneratorInterface $generator, UrlGeneratorInterface $contentRouter, LoggerInterface $logger)
    {
        parent::__construct($generator);
        $this->contentRouter = $contentRouter;
        $this->container = $container;
        $this->logger = $logger;
    }

    /**
     * Add a voter to decide on current item.
     *
     * @param CurrentItemVoterInterface $voter
     * @param int                       $priority High numbers can vote first
     *
     * @see CurrentItemVoterInterface
     */
    public function addCurrentItemVoter(CurrentItemVoterInterface $voter, $priority)
    {
        if (!isset($this->voters[$priority])) {
            $this->voters[$priority] = array();
        }
        $this->voters[$priority][] = $voter;
        $this->sortedVoters = false;
    }

    /**
     * Get the ordered list of all menu item voters.
     *
     * @return CurrentItemVoterInterface[]
     */
    private function getVoters()
    {
        if ($this->sortedVoters === false) {
            $this->sortedVoters = array();
            krsort($this->voters);

            foreach ($this->voters as $voters) {
                $this->sortedVoters = array_merge($this->sortedVoters, $voters);
            }
        }

        return $this->sortedVoters;
    }

    /**
     * Create a MenuItem from a NodeInterface instance
     *
     * @param NodeInterface $node
     *
     * @return MenuItem
     */
    public function createFromNode(NodeInterface $node)
    {
        $item = $this->createItem($node->getName(), $node->getOptions(), $node);
        if (!empty($item)) {
            foreach ($node->getChildren() as $childNode) {
                if ($childNode instanceof NodeInterface) {
                    $child = $this->createFromNode($childNode);
                    if (!empty($child)) {
                        $item->addChild($child);
                    }
                }
            }
        }

        return $item;
    }

    /**
     * Create a MenuItem. This triggers the voters to decide if its the current
     * item.
     *
     * @param string        $name    the menu item name
     * @param array         $options options for the menu item, we care about
     *                               'content'
     * @param NodeInterface $node    optional node this item is created from,
     *                               passed to the voters.
     *
     * @return MenuItem|null returns null if no route can be built for this menu item
     */
    public function createItem($name, array $options = array(), NodeInterface $node = null)
    {
        try {
            $options['uri'] = $this->contentRouter->generate($options['content'], $options['routeParameters'], $options['routeAbsolute']);
        } catch (RouteNotFoundException $e) {
            return null;
        }
        unset($options['route']);

        $request = $this->container->get('request');
        $current = $this->isCurrentItem($request, $options, $node);

        $item = parent::createItem($name, $options);
        if ($current) {
            $item->setCurrent(true);
        }

        return $item;
    }

    /**
     * Cycle through all voters. If any votes YES, this is the current item. If
     * any votes NO cycling stops. While we get ABSTAIN we continue cycling.
     *
     * @param Request       $request The request fetched from the container
     * @param array         $options The menu item options
     * @param NodeInterface $node    Optional
     *
     * @return bool
     *
     * @see CurrentItemVoterInterface
     */
    private function isCurrentItem(Request $request, array $options, NodeInterface $node = null)
    {
        foreach ($this->getVoters() as $voter) {
            try {
                switch ($voter->isCurrentItem($request, $options, $node)) {
                    case CurrentItemVoterInterface::VOTE_YES:
                        return true;
                    case CurrentItemVoterInterface::VOTE_NO:
                        return false;
                    // on abstain just continue
                }
            } catch (\Exception $e) {
                // ignore
                $this->logger->error(sprintf('Current item voter failed with: "%s"', $e->getMessage()));
            }
        }

        return false;
    }
}
