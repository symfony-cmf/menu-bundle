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
     * @param UrlGeneratorInterface    $generator     for the parent class
     * @param UrlGeneratorInterface    $contentRouter to generate routes when
     *                                                content is set
     * @param EventDispatcherInterface $dispatcher    to dispatch the CREATE_ITEM_FROM_NODE event.
     * @param LoggerInterface          $logger
     */
    public function __construct(
        UrlGeneratorInterface $contentRouter
    )
    {
        $this->contentRouter = $contentRouter;
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
}
