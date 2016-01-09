<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Templating;

use Knp\Menu\ItemInterface;
use Knp\Menu\NodeInterface;
use Knp\Menu\FactoryInterface;
use Doctrine\ODM\PHPCR\DocumentManager;
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
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var FactoryInterface
     */
    private $menuFactory;
    private $contentObjectKey;
    private $routeNameKey;

    /**
     * @param DocumentManager  $documentManager
     * @param FactoryInterface $menuFactory
     * @param string           $contentObjectKey The name of the request attribute holding
     *                                           the current content object
     * @param string           $routeNameKey     The name of the request attribute holding
     *                                           the name of the current route
     */
    public function __construct(DocumentManager $documentManager, FactoryInterface $menuFactory, $contentObjectKey = RouteObjectInterface::CONTENT_OBJECT, $routeNameKey = RouteObjectInterface::ROUTE_NAME)
    {
        $this->documentManager = $documentManager;
        $this->menuFactory = $menuFactory;
        $this->contentObjectKey = $contentObjectKey;
        $this->routeNameKey = $routeNameKey;
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
     * @param Request $request
     *
     * @return MenuNode|null
     */
    public function getCurrentItem(Request $request)
    {
        $node = $this->getCurrentNode($request);

        if (!$node instanceof NodeInterface) {
            return null;
        }

        return $this->menuFactory->createItem($node->getName(), $node->getOptions());
    }

    public function getCurrentNode(Request $request)
    {
        $node = null;
        if ($request->attributes->has($this->contentObjectKey)) {
            $content = $request->attributes->get($this->contentObjectKey);

            $node = $this->documentManager->getRepository('CmfMenuBundle:MenuNode')
                ->findOneBy(array('content' => $content));
        } elseif ($request->attributes->has($this->routeNameKey)) {
            $route = $request->attributes->get($this->routeNameKey);

            $nodes = $this->documentManager->getRepository('CmfMenuBundle:MenuNode')
                ->findBy(array('route' => $route));
            if (1 === count($nodes)) {
                $node = $nodes->first();
            } else {
                foreach ($nodes as $n) {
                    if ('route' === $n->getLinkType()) {
                        $node = $n;
                        break;
                    }
                }
            }
        }

        return $node;
    }

    public function getName()
    {
        return 'cmf_menu';
    }
}
