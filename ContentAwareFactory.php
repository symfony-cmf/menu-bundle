<?php

namespace Symfony\Cmf\Bundle\MenuBundle;

use Knp\Menu\NodeInterface;
use Knp\Menu\Silex\RouterAwareFactory;
use Knp\Menu\MenuItem;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This factory builds menu items from the menu nodes and builds urls based on
 * the content these menu nodes stand for.
 */
class ContentAwareFactory extends RouterAwareFactory
{
    protected $contentRouter;
    protected $container;

    /**
     * @param ContainerInterface $container to fetch the request in order to determine
     *      whether this is the current menu item
     * @param UrlGeneratorInterface $generator for the parent class
     * @param UrlGeneratorInterface $contentRouter to generate routes when
     *      content is set
     * @param string $contentKey
     * @param string $routeName the name of the route to use. DynamicRouter
     *      ignores this.
     */
    public function __construct(ContainerInterface $container, UrlGeneratorInterface $generator, UrlGeneratorInterface $contentRouter, $contentKey, $routeName = null)
    {
        parent::__construct($generator);
        $this->contentRouter = $contentRouter;
        $this->container = $container;
        $this->contentKey = $contentKey;
        $this->routeName = $routeName;
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
        $item = $this->createItem($node->getName(), $node->getOptions());
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
     * Create a MenuItem
     *
     * @param string $name    the menu item name
     * @param array  $options options for the menu item, we care about 'content'
     *
     * @return MenuItem|null returns null if no route can be built for this menu item
     */
    public function createItem($name, array $options = array())
    {
        $current = false;
        if (!empty($options['content'])) {
            try {
                $request = $this->container->get('request');
                $currentUriPrefix = null;

                if ($options['content'] instanceof Route && $options['content']->getOption('currentUriPrefix')) {
                    $currentUriPrefix = $options['content']->getOption('currentUriPrefix');
                    $currentUriPrefix = preg_replace('#\{_locale\}#', $request->getLocale(), $currentUriPrefix);
                }

                if ($currentUriPrefix !== null && 0 === strpos($request->getPathinfo(), $currentUriPrefix)) {
                    $current = true;
                } elseif ($request->attributes->get($this->contentKey) === $options['content']) {
                    $current = true;
                }
            } catch (\Exception $e) {}

            try {
                $options['uri'] = $this->contentRouter->generate($options['content'], $options['routeParameters'], $options['routeAbsolute']);
            } catch (RouteNotFoundException $e) {
                return null;
            }

            unset($options['route']);
        }

        $item = parent::createItem($name, $options);
        if ($current) {
            $item->setCurrent(true);
        }

        return $item;
    }
}
