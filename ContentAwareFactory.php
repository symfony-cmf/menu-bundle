<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle;

use Knp\Menu\MenuFactory;
use Knp\Menu\ItemInterface;
use Knp\Menu\NodeInterface;
use Knp\Menu\MenuItem;

use Psr\Log\LoggerInterface;

use Symfony\Cmf\Bundle\MenuBundle\Event\Events;
use Symfony\Cmf\Bundle\MenuBundle\Model\Menu;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

use Symfony\Cmf\Bundle\MenuBundle\Voter\VoterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Cmf\Bundle\MenuBundle\Event\CreateMenuItemFromNodeEvent;

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
class ContentAwareFactory extends MenuFactory
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $contentRouter;

    /**
     * Valid link types values, e.g. route, uri, content
     */
    protected $linkTypes = array();

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
     * Whether to return null or a MenuItem without any URL if no URL can be
     * found for a MenuNode.
     *
     * @var boolean
     */
    private $allowEmptyItems;

    /**
     * @param UrlGeneratorInterface    $generator     for the parent class
     * @param UrlGeneratorInterface    $contentRouter to generate routes when
     *                                                content is set
     * @param EventDispatcherInterface $dispatcher    to dispatch the CREATE_ITEM_FROM_NODE event.
     * @param LoggerInterface          $logger
     */
    public function __construct(
        UrlGeneratorInterface $generator,
        UrlGeneratorInterface $contentRouter,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger
    )
    {
        $this->generator = $generator;
        $this->contentRouter = $contentRouter;
        $this->linkTypes = array('route', 'uri', 'content');
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    /**
     * Return the linkTypes handled by this factory.
     * e.g. array('uri', 'route', 'content').
     *
     * @return array
     */
    public function getLinkTypes()
    {
        return $this->linkTypes;
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
     * @param int            $priority High numbers can vote first
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
     * Create a MenuItem from a NodeInterface instance.
     *
     * @param NodeInterface $node
     *
     * @return MenuItem|null If allowEmptyItems is false and this node has
     *                       neither URL nor route nor a content that has a
     *                       route, returns null.
     */
    public function createFromNode(NodeInterface $node)
    {
        $event = new CreateMenuItemFromNodeEvent($node, $this);
        $this->dispatcher->dispatch(Events::CREATE_ITEM_FROM_NODE, $event);

        if ($event->isSkipNode()) {
            if ($node instanceof Menu) {
                // create an empty menu root to avoid the knp menu from failing.
                return $this->createItem('');
            }

            return null;
        }

        $item = $event->getItem() ?: $this->createItem($node->getName(), $node->getOptions());

        if (empty($item)) {
            return null;
        }

        if ($event->isSkipChildren()) {
            return $item;
        }

        return $this->addChildrenFromNode($node->getChildren(), $item);
    }

    /**
     * Create menu items from a list of menu nodes and add them to $item.
     *
     * @param NodeInterface[] $node The menu nodes to create.
     * @param ItemInterface   $item The menu item to add the children to.
     *
     * @return ItemInterface
     */
    public function addChildrenFromNode($nodes, ItemInterface $item)
    {
        foreach ($nodes as $childNode) {
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
     * You can add custom link types by overwriting this method and calling the
     * parent - setting the URI option and the linkType to "uri".
     *
     * @param string $name    the menu item name
     * @param array  $options options for the menu item, we care about
     *                        'content'
     *
     * @return MenuItem|null Returns null if no route can be built for this menu item,
     *
     * @throws \RuntimeException If the stored link type is not known.
     */
    public function createItem($name, array $options = array())
    {
        $options = array_merge(array(
            'content' => null,
            'routeParameters' => array(),
            'routeAbsolute' => false,
            'uri' => null,
            'route' => null,
            'linkType' => null,
        ), $options);

        if (null === $options['linkType']) {
            $options['linkType'] = $this->determineLinkType($options);
        }

        $this->validateLinkType($options['linkType']);

        switch ($options['linkType']) {
            case 'content':
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
                unset($options['route']);
                break;
            case 'uri':
                unset($options['route']);
                break;
            case 'route':
                unset($options['uri']);

                try {
                    $options['uri'] = $this->generator->generate(
                        $options['route'],
                        $options['routeParameters'],
                        $options['routeAbsolute']
                    );

                    unset($options['route']);
                } catch (RouteNotFoundException $e) {
                    $this->logger->error(sprintf('%s : %s', $name, $e->getMessage()));

                    if (!$this->allowEmptyItems) {
                        return null;
                    }
                }
                break;
            default:
                throw new \RuntimeException(sprintf('Internal error: unexpected linkType "%s"', $options['linkType']));
        }

        $item = parent::createItem($name, $options);
        $item->setExtra('content', $options['content']);

        $current = $this->isCurrentItem($item);

        if ($current) {
            $item->setCurrent(true);
        }

        return $item;
    }

    /**
     * If linkType not specified, we can determine it from
     * existing options
     */
    protected function determineLinkType($options)
    {
        if (!empty($options['uri'])) {
            return 'uri';
        }

        if (!empty($options['route'])) {
            return 'route';
        }

        if (!empty($options['content'])) {
            return 'content';
        }

        return 'uri';
    }

    /**
     * Ensure that we have a valid link type.
     *
     * @param string $linkType
     *
     * @throws \InvalidArgumentException if $linkType is not one of the known
     *                                   link types
     */
    protected function validateLinkType($linkType)
    {
        if (!in_array($linkType, $this->linkTypes)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid link type "%s". Valid link types are: "%s"',
                $linkType,
                implode(',', $this->linkTypes)
            ));
        }
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
