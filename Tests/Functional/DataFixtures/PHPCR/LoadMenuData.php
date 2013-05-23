<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Functional\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Cmf\Bundle\MenuBundle\Document\MenuNode;

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

        $menu = new MenuNode;
        $menu->setName('Test Menu');
        $menu->setParent($root);
        $manager->persist($menu);
        $manager->flush();
    }
}
