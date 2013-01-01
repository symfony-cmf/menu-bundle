<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Factory;

use Symfony\Cmf\Bundle\MenuBundle\Factory\MenuFactory;

class MenuFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFromArrayWithoutChildren()
    {
        $factory = new MenuFactory();
        $array = array(
            'name' => 'joe',
            'uri' => '/foobar',
        );
        $item = $factory->createFromArray($array);
        $this->assertEquals('joe', $item->getName());
        $this->assertEquals('/foobar', $item->getUri());
        $this->assertEmpty($item->getAttributes());
        $this->assertEmpty($item->getChildren());
    }

    public function testFromArrayWithChildren()
    {
        $factory = new MenuFactory();
        $array = array(
            'name' => 'joe',
            'children' => array(
                'jack' => array(
                    'name' => 'jack',
                    'label' => 'Jack',
                ),
                array(
                    'name' => 'john'
                )
            ),
        );
        $item = $factory->createFromArray($array);
        $this->assertEquals('joe', $item->getName());
        $this->assertEmpty($item->getAttributes());
        $this->assertCount(2, $item->getChildren()); // @todo: Implement countable ?
    }

    public function testFromArrayWithChildrenOmittingName()
    {
        $factory = new MenuFactory();
        $array = array(
            'name' => 'joe',
            'children' => array(
                'jack' => array(
                    'label' => 'Jack',
                ),
                'john' => array(
                    'label' => 'John'
                )
            ),
        );

        $item = $factory->createFromArray($array);
        $this->assertEquals('joe', $item->getName());
        $this->assertEmpty($item->getAttributes());
        $this->assertCount(2, $item->getChildren()); // @todo: Implement countable ?
    }
}

