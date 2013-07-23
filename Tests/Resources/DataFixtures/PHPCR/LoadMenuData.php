<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu;
use Doctrine\ODM\PHPCR\Document\Generic;

class LoadMenuData implements FixtureInterface, DependentFixtureInterface
{
    public function getDependencies()
    {
        return array(
            'Symfony\Cmf\Component\Testing\DataFixtures\PHPCR\LoadBaseData',
        );
    }

    public function load(ObjectManager $manager)
    {
        $root = $manager->find(null, '/test');
        $menuRoot = new Generic;
        $menuRoot->setNodename('menus');
        $menuRoot->setParent($root);
        $manager->persist($menuRoot);

        $menu = new Menu;
        $menu->setName('test-menu');
        $menu->setLabel('Test Menu');
        $menu->setParent($menuRoot);
        $manager->persist($menu);

        $menuNode = new MenuNode;
        $menuNode->setParent($menu);
        $menuNode->setLabel('item-1');
        $menuNode->setName('item-1');
        $manager->persist($menuNode);

        $menuNode = new MenuNode;
        $menuNode->setParent($menu);
        $menuNode->setLabel('This node has a URI');
        $menuNode->setUri('http://www.example.com');
        $menuNode->setName('item-2');
        $manager->persist($menuNode);

        $subNode = new MenuNode;
        $subNode->setParent($menuNode);
        $subNode->setLabel('@todo this node should have content');
        $subNode->setName('sub-item-1');
        $manager->persist($subNode);

        $subNode = new MenuNode;
        $subNode->setParent($menuNode);
        $subNode->setLabel('This node has an assigned route');
        $subNode->setName('sub-item-2');
        $subNode->setRoute('link_test_route');
        $manager->persist($subNode);

        $subNode = new MenuNode;
        $subNode->setParent($menuNode);
        $subNode->setLabel('This node has an assigned route with parameters');
        $subNode->setName('sub-item-3');
        $subNode->setRoute('link_test_route_with_params');
        $subNode->setRouteParameters(array('foo' => 'bar', 'bar' => 'foo'));
        $manager->persist($subNode);

        $menuNode = new MenuNode;
        $menuNode->setParent($menu);
        $menuNode->setLabel('item-3');
        $menuNode->setName('item-3');
        $manager->persist($menuNode);

        $menu = new Menu;
        $menu->setName('another-menu');
        $menu->setLabel('Another Menu');
        $menu->setParent($menuRoot);
        $manager->persist($menu);

        $menuNode = new MenuNode;
        $menuNode->setParent($menu);
        $menuNode->setLabel('This node has uri, route and content set. but linkType is set to route');
        $menuNode->setLinkType('route');
        $menuNode->setUri('http://www.example.com');
        $menuNode->setRoute('link_test_route');
        $menuNode->setName('item-1');
        $manager->persist($menuNode);

        $menuNode = new MenuNode;
        $menuNode->setParent($menu);
        $menuNode->setLabel('item-2');
        $menuNode->setName('item-2');
        $manager->persist($menuNode);

        $manager->flush();
    }
}
