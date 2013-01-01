<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Document;
use Symfony\Cmf\Bundle\MenuBundle\Document\MenuItem;

class MenuItemTest extends \PHPUnit_Framework_Testcase
{
    public function setUp()
    {
        $c1 = new MenuItem;
        $c1->setLabel('Child 1');
        $c2 = new MenuItem;
        $c2->setLabel('Child 2');
        $this->content = new \StdClass;
        $this->parentItem = new MenuItem;
        $this->item = new MenuItem;
        $this->item->setId('/foo/bar')
            ->setParent($this->parentItem)
            ->setName('test')
            ->setLabel('Test')
            ->setUri('http://www.example.com')
            ->setRoute('test_route')
            ->setContent($this->content)
            ->setWeak(false)
            ->setAttributes(array('foo' => 'bar'))
            ->setChildrenAttributes(array('bar' => 'foo'))
            ->setExtras(array('far' => 'boo'));
    }

    public function testGetters()
    {
        $this->assertSame($this->parentItem, $this->item->getParent());
        $this->assertEquals('test', $this->item->getName());
        $this->assertEquals('Test', $this->item->getLabel());
        $this->assertEquals('http://www.example.com', $this->item->getUri());
        $this->assertEquals('test_route', $this->item->getRoute());
        $this->assertSame($this->content, $this->item->getContent());
        $this->assertFalse($this->item->getWeak());
        $this->assertEquals(array('foo' => 'bar'), $this->item->getAttributes());
        $this->assertEquals('bar', $this->item->getAttribute('foo'));
        $this->assertEquals(array('bar' => 'foo'), $this->item->getChildrenAttributes());
        $this->assertEquals(array('far' => 'boo'), $this->item->getExtras());

        $this->parentItem = new MenuItem;
        $this->item->setPosition($this->parentItem, 'FOOO');
        $this->assertSame($this->parentItem, $this->item->getParent());
        $this->assertEquals('FOOO', $this->item->getName());
    }

    public function testAddChild()
    {
        $c1 = new MenuItem;
        $c2 = new MenuItem;
        $m = new MenuItem;
        $m->addChild($c1);
        $m->addChild($c2);

        $children = $m->getChildren();
        $this->assertCount(2, $children);
        $this->assertSame($m, $children[0]->getParent());
    }
}
