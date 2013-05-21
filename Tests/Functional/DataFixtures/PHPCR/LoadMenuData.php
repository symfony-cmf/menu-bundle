<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Functional\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Cmf\Bundle\MenuBundle\Document\MenuNode;
use Symfony\Cmf\Bundle\MenuBundle\Document\Menu;

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

        $menu = new Menu;
        $menu->setName('test-menu');
        $menu->setParent($root);
        $manager->persist($menu);

        $menuNode = new MenuNode;
        $menuNode->setParent($menu);
        $menuNode->setLabel('item1');
        $menuNode->setName('item1');
        $manager->persist($menuNode);

        $manager->flush();
    }
}
