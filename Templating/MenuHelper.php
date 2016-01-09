<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Templating;

use Knp\Menu\ItemInterface;
use Knp\Menu\NodeInterface;
use Knp\Menu\FactoryInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\Helper\Helper;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNode;

/**
 * A templating helper providing faster solutions to
 * the KnpMenu alternatives.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class MenuHelper extends Helper
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var FactoryInterface
     */
    private $menuFactory;
    private $managerName;
    private $contentObjectKey;
    private $routeNameKey;

    /**
     * @param ManagerRegistry  $managerRegistry
     * @param FactoryInterface $menuFactory
     * @param string           $contentObjectKey The name of the request attribute holding
     *                                           the current content object
     * @param string           $routeNameKey     The name of the request attribute holding
     *                                           the name of the current route
     */
    public function __construct(ManagerRegistry $managerRegistry, FactoryInterface $menuFactory, $contentObjectKey = RouteObjectInterface::CONTENT_OBJECT, $routeNameKey = RouteObjectInterface::ROUTE_NAME)
    {
        $this->managerRegistry = $managerRegistry;
        $this->menuFactory = $menuFactory;
        $this->contentObjectKey = $contentObjectKey;
        $this->routeNameKey = $routeNameKey;
    }

    /**
     * Set the object manager name to use for this loader. If not set, the
     * default manager as decided by the manager registry will be used.
     *
     * @param string|null $managerName
     */
    public function setManagerName($managerName)
    {
        $this->managerName = $managerName;
    }

    /**
     * Generates an array of breadcrumb items by traversing
     * up the tree from the current node.
     *
     * @param NodeInterface $node            The current menu node (use {@link getCurrentNode} to get it)
     * @param bool          $includeMenuRoot Whether to include the menu root as breadcrumb item
     *
     * @return array An array with breadcrumb items (each item has the following keys: label, uri, item)
     */
    public function getBreadcrumbArray(NodeInterface $node, $includeMenuRoot = true)
    {
        $item = $this->menuFactory->createItem($node->getName(), $node->getOptions());

        $breadcrumbs = array(
            array(
                'label' => $item->getLabel(),
                'uri' => $item->getUri(),
                'item' => $item,
            ),
        );

        $parent = $node->getParentObject();
        if (!$parent instanceof MenuNode) {
            // We assume the root of the menu is reached
            return $includeMenuRoot ? $breadcrumbs : array();
        }

        return array_merge($this->getBreadcrumbArray($parent, $includeMenuRoot), $breadcrumbs);
    }

    /**
     * Tries to find the current item from the request.
     *
     * The returned item does *not* include the parent and children,
     * in order to minimalize the overhead.
     *
     * @param Request $request
     *
     * @return ItemInterface|null
     */
    public function getCurrentItem(Request $request)
    {
        $node = $this->getCurrentNode($request);

        if (!$node instanceof NodeInterface) {
            return;
        }

        return $this->menuFactory->createItem($node->getName(), $node->getOptions());
    }

    /**
     * Retrieves the current node based on a Request.
     *
     * It uses some special Request attributes that are managed by
     * the CmfRoutingBundle:
     *
     *  * RouteObjectInterface::CONTENT_OBJECT to match a menu node by the refering content
     *  * RouteObjectInterface::ROUTE_NAME to match a menu node by the refering route name
     *
     * @return NodeInterface|null
     */
    public function getCurrentNode(Request $request)
    {
        if ($request->attributes->has($this->contentObjectKey)) {
            $content = $request->attributes->get($this->contentObjectKey);

            return $this->managerRegistry->getManager($this->managerName)->getRepository('CmfMenuBundle:MenuNode')->findOneBy(array('content' => $content));
        }
        
        if ($request->attributes->has($this->routeNameKey)) {
            $route = $request->attributes->get($this->routeNameKey);

            $nodes = $this->managerRegistry->getManager($this->managerName)->getRepository('CmfMenuBundle:MenuNode')->findBy(array('route' => $route));
            if (1 === count($nodes)) {
                return $nodes->first();
            } else {
                foreach ($nodes as $node) {
                    if ('route' === $node->getLinkType()) {
                        return $node;
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cmf_menu';
    }
}
