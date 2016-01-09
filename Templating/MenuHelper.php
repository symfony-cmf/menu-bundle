<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Templating;

use Knp\Menu\ItemInterface;
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
    private $contentObjectKey;

    /**
     * @param DocumentManager $documentManager
     * @param string          $contentObjectKey The name of the request attribute holding
     *                                          the current content object
     * @param string          $routeNAmeKey     The name of the request attribute holding
     *                                          the name of the current route
     */
    public function __construct(DocumentManager $documentManager, $contentObjectKey = RouteObjectInterface::CONTENT_OBJECT, $routeNameKey = RouteObjectInterface::ROUTE_NAME)
    {
        $this->documentManager = $documentManager;
        $this->contentObjectKey = $contentObjectKey;
        $this->routeNameKey = $routeNameKey;
    }

    /**
     * Generates an array of breadcrumb items by traversing
     * up the tree from the current item.
     *
     * @param ItemInterface $item The current menu item
     *
     * @return array An array with breadcrumb items (each item has the following keys: label, uri, item)
     */
    public function getBreadcrumbArray(ItemInterface $item)
    {
        $breadcrumbs = array(
            array(
                'label' => $item->getLabel(),
                'uri' => $item->getUri(),
                'item' => $item
            ),
        );

        if (!$item instanceof MenuNode) {
            // We assume the root of the menu is reached
            return $breadcrumbs;
        }

        return array_merge($breadcrumbs, $this->getBreadcrumbArray($item->getParentObject()));
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
        $node = null;
        if ($request->attributes->has($this->contentObjectKey)) {
            $content = $request->attributes->get($this->contentObjectKey);

            $node = $this->documentManager->getRepository('CmfMenuBundle:MenuNode')
                ->findOneBy(array('content' => $content));
        } elseif ($request->attributes->has($this->routeNameKey)) {
            $route = $request->attributes->get($this->routeNameKey);

            $node = $this->documentManager->getRepository('CmfMenuBundle:MenuNode')
                ->findOneBy(array('route' => $rotue));
        }

        return $node;
    }

    public function getName()
    {
        return 'cmf_menu';
    }
}
