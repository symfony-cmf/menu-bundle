<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\DocumentManager;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu;
use Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\Document\Content;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route;
use PHPCR\Util\NodeHelper;

class LoadMenuData implements FixtureInterface
{
    protected $menuRoot;
    protected $routeRoot;

    public function load(ObjectManager $manager)
    {
        NodeHelper::createPath($manager->getPhpcrSession(), '/test/menus');
        NodeHelper::createPath($manager->getPhpcrSession(), '/test/routes/contents');
        $this->menuRoot = $manager->find(null, '/test/menus');
        $this->routeRoot = $manager->find(null, '/test/routes');

        $this->loadMenu($manager);

        $manager->flush();
    }

    protected function loadMenu(DocumentManager $manager)
    {
        $route = new Route();
        $route->setName('content-1');
        $route->setParentDocument($this->routeRoot);
        $manager->persist($route);

        $content = new Content();
        $content->setTitle('Menu Item Content 1');
        $content->setId('/test/content-menu-item-1');
        $content->addRoute($route);

        $menu = new Menu();
        $menu->setName('test-menu');
        $menu->setLabel('Test Menu');
        $menu->setParentDocument($this->menuRoot);
        $manager->persist($menu);

        $menuNode = new MenuNode();
        $menuNode->setParentDocument($menu);
        $menuNode->setLabel('item-1');
        $menuNode->setName('item-1');
        $manager->persist($menuNode);

        $menuNode = new MenuNode();
        $menuNode->setParentDocument($menu);
        $menuNode->setLabel('This node has a URI');
        $menuNode->setUri('http://www.example.com');
        $menuNode->setName('item-2');
        $manager->persist($menuNode);

        $subNode = new MenuNode();
        $subNode->setParentDocument($menuNode);
        $subNode->setLabel('This node has content');
        $subNode->setName('sub-item-1');
        $subNode->setContent($content);
        $manager->persist($subNode);

        $content->addMenuNode($subNode);

        $subNode = new MenuNode();
        $subNode->setParentDocument($menuNode);
        $subNode->setLabel('This node has an assigned route');
        $subNode->setName('sub-item-2');
        $subNode->setRoute('link_test_route');
        $manager->persist($subNode);

        $subNode = new MenuNode();
        $subNode->setParentDocument($menuNode);
        $subNode->setLabel('This node has an assigned route with parameters');
        $subNode->setName('sub-item-3');
        $subNode->setRoute('link_test_route_with_params');
        $subNode->setRouteParameters(array('foo' => 'bar', 'bar' => 'foo'));
        $manager->persist($subNode);

        $menuNode = new MenuNode();
        $menuNode->setParentDocument($menu);
        $menuNode->setLabel('item-3');
        $menuNode->setName('item-3');
        $manager->persist($menuNode);

        $menu = new Menu();
        $menu->setName('another-menu');
        $menu->setLabel('Another Menu');
        $menu->setParentDocument($this->menuRoot);
        $manager->persist($menu);

        $menuNode = new MenuNode();
        $menuNode->setParentDocument($menu);
        $menuNode->setLabel('This node has uri, route and content set. but linkType is set to route');
        $menuNode->setLinkType('route');
        $menuNode->setUri('http://www.example.com');
        $menuNode->setRoute('link_test_route');
        $menuNode->setName('item-1');
        $manager->persist($menuNode);

        $menuNode = new MenuNode();
        $menuNode->setParentDocument($menu);
        $menuNode->setLabel('item-2');
        $menuNode->setName('item-2');
        $manager->persist($menuNode);

        $manager->persist($content);
    }
}
