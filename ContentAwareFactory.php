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
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowChecker;

use Psr\Log\LoggerInterface;

use Symfony\Cmf\Bundle\MenuBundle\Voter\VoterInterface;

/**
 * This factory builds menu items from the menu nodes and builds urls based on
 * the content these menu nodes stand for.
 *
 * Using the allowEmptyItems option you can control whether menu nodes for
 * which no URL is found should still create menu entries or be skipped.
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
    private $voters = array();

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * Whether to return null or a MenuItem without any URL if no URL can be
     * found for a MenuNode.
     *
     * @var boolean
     */
    private $allowEmptyItems;

    /**
     * @param ContainerInterface $container to fetch the request in order to determine
     *      whether this is the current menu item
     * @param UrlGeneratorInterface $generator for the parent class
     * @param UrlGeneratorInterface $contentRouter to generate routes when
     *      content is set
     */
    public function __construct(
        UrlGeneratorInterface $generator,
        UrlGeneratorInterface $contentRouter,
        SecurityContextInterface $securityContext = null,
        LoggerInterface $logger
    )
    {
        parent::__construct($generator);
        $this->contentRouter = $contentRouter;
        $this->securityContext = $securityContext;
        $this->logger = $logger;
    }

    /**
     * Whether to return a MenuItem without an URL or null when a MenuNode has
     * no URL that can be found.
     *
     * @param boolean $allowEmptyItems
     */
    public function setAllowEmptyItems($allowEmptyItems)
    {
        $this->allowEmptyItems = $allowEmptyItems;
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
     * @return MenuItem|null if allowEmptyItems is false and this node has
     *     neither URL nor route nor a content that has a route, this method
     *     returns null.
     */
    public function createFromNode(NodeInterface $node)
    {
        $item = $this->createItem($node->getName(), $node->getOptions(), $node);

        if (empty($item)) {
            return null;
        }

        foreach ($node->getChildren() as $childNode) {
            if ($this->securityContext
                && ! $this->securityContext->isGranted(PublishWorkflowChecker::VIEW_ATTRIBUTE, $childNode)
            ) {
                continue;
            }

            if ($childNode instanceof NodeInterface) {
                $child = $this->createFromNode($childNode);
                if (!empty($child)) {
                    $item->addChild($child);
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
        $options = array_merge(array(
            'content' => null,
            'routeParameters' => array(),
            'routeAbsolute' => false,
            'uri' => null,
            'route' => null,
        ), $options);

        if (empty($options['uri']) && empty($options['route'])) {
            try {
                $options['uri'] = $this->contentRouter->generate(
                    $options['content'],
                    $options['routeParameters'],
                    $options['routeAbsolute']
                );
            } catch (RouteNotFoundException $e) {
                if (!$this->allowEmptyItems) {
                    return null;
                }
            }
        }

        $item = parent::createItem($name, $options);
        $item->setAttribute('content', $options['content']);

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
