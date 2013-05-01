<?php

namespace Symfony\Cmf\Bundle\MenuBundle;

use Knp\Menu\Silex\RouterAwareFactory;
use Knp\Menu\ItemInterface;
use Knp\Menu\NodeInterface;
use Knp\Menu\MenuItem;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Psr\Log\LoggerInterface;

use Symfony\Cmf\Bundle\MenuBundle\Voter\VoterInterface;

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
     * List of priority => array of VoterInterface
     *
     * @var array
     */
    private $voters;

    /**
     * Sorted list of current item voters
     *
     * @var VoterInterface[]
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
    public function __construct(UrlGeneratorInterface $generator, UrlGeneratorInterface $contentRouter, LoggerInterface $logger)
    {
        parent::__construct($generator);
        $this->contentRouter = $contentRouter;
        $this->logger = $logger;
    }

    /**
     * Add a voter to decide on current item.
     *
     * @param VoterInterface $voter
     * @param int                       $priority High numbers can vote first
     *
     * @see VoterInterface
     */
    public function addCurrentItemVoter(VoterInterface $voter)
    {
        $this->voters[] = $voter;
    }

    /**
     * Get the ordered list of all menu item voters.
     *
     * @return VoterInterface[]
     */
    private function getVoters()
    {
        return $this->voters;
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

        $item = parent::createItem($name, $options);

        $current = $this->isCurrentItem($item);
        if ($current) {
            $item->setCurrent(true);
        }

        return $item;
    }

    /**
     * Cycle through all voters. If any votes true, this is the current item. If
     * any votes false cycling stops. Continue cycling while we get null.
     *
     * @param ItemInterface $item the newly created menu item
     *
     * @return bool
     *
     * @see VoterInterface
     */
    private function isCurrentItem(ItemInterface $item)
    {
        foreach ($this->getVoters() as $voter) {
            try {
                $vote = $voter->matchItem($item);
                if (null ===$vote) {
                    continue;
                }

                return $vote;
            } catch (\Exception $e) {
                // ignore
                $this->logger->error(sprintf('Current item voter failed with: "%s"', $e->getMessage()));
            }
        }

        return false;
    }
}
