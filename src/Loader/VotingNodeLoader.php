<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Loader;

use Knp\Menu\FactoryInterface;
use Knp\Menu\Loader\NodeLoader;
use Knp\Menu\NodeInterface;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu;
use Symfony\Cmf\Bundle\MenuBundle\Event\CreateMenuItemFromNodeEvent;
use Symfony\Cmf\Bundle\MenuBundle\Event\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class VotingNodeLoader extends NodeLoader
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var FactoryInterface
     */
    private $menuFactory;

    public function __construct(FactoryInterface $factory, EventDispatcherInterface $dispatcher)
    {
        $this->menuFactory = $factory;
        $this->dispatcher = $dispatcher;
    }

    public function load($data)
    {
        if (!$this->supports($data)) {
            throw new \InvalidArgumentException(sprintf(
                'NodeLoader can only handle data implementing NodeInterface, "%s" given.',
                \is_object($data) ? \get_class($data) : \gettype($data)
            ));
        }
        $event = new CreateMenuItemFromNodeEvent($data);
        $this->dispatcher->dispatch(Events::CREATE_ITEM_FROM_NODE, $event);

        if ($event->isSkipNode()) {
            if ($data instanceof Menu) {
                // create an empty menu root to avoid the knp menu from failing.
                return $this->menuFactory->createItem('');
            }

            return;
        }

        $item = $event->getItem() ?: $this->menuFactory->createItem($data->getName(), $data->getOptions());

        if (empty($item)) {
            return;
        }

        if ($event->isSkipChildren()) {
            return $item;
        }

        foreach ($data->getChildren() as $childNode) {
            if ($childNode instanceof NodeInterface) {
                $child = $this->load($childNode);
                if (!empty($child)) {
                    $item->addChild($child);
                }
            }
        }

        return $item;
    }
}
