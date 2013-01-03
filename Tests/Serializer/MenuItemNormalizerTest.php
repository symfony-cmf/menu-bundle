<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Serializer;
use Symfony\Cmf\Bundle\MenuBundle\Serializer\MenuItemNormalizer;
use Symfony\Cmf\Bundle\MenuBundle\Document\MenuItem;

class MenuItemNormalizerTest extends \PHPUnit_Framework_Testcase
{
    public function setUp()
    {
        $this->dm = $this->getMockBuilder('Doctrine\ODM\PHPCR\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->classMetadata = $this->getMockBuilder('Doctrine\ODM\PHPCR\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $this->normalizer = new MenuItemNormalizer($this->dm);

        $this->content = new \stdClass;;
        $this->item = new MenuItem;
        $this->item->setId('/foo/bar')
            ->setName('test')
            ->setLabel('Test')
            ->setUri('http://www.example.com')
            ->setRoute('test_route')
            ->setContent($this->content)
            ->setWeak(false)
            ->setAttributes(array('foo' => 'bar'))
            ->setChildrenAttributes(array('bar' => 'foo'))
            ->setExtras(array('far' => 'boo'));

        $c1 = new MenuItem;
        $c1->setLabel('Child 1');
        $this->item->addChild($c1);

        $this->expectedMenu = array (
            'id' => '/foo/bar',
            'name' => 'test',
            'label' => 'Test',
            'uri' => 'http://www.example.com',
            'route' => 'test_route',
            'content' => '/this/is/content',
            'weak' => false,
            'attributes' => array (
                'foo' => 'bar',
            ),
            'extras' => array (
                'far' => 'boo',
            ),
            'childrenAttributes' => array (
                'bar' => 'foo',
            ),
            'children' => array (
                0 => array (
                    'id' => NULL,
                    'name' => NULL,
                    'label' => 'Child 1',
                    'uri' => NULL,
                    'route' => NULL,
                    'content' => '',
                    'weak' => true,
                    'attributes' => array (
                    ),
                    'extras' => NULL,
                    'childrenAttributes' => array (
                    ),
                    'children' => array (
                    ),
                ),
            ),
        );
    }

    public function testNormalize()
    {
        $this->dm->expects($this->exactly(2))
            ->method('getClassMetadata')
            ->with('Symfony\Cmf\Bundle\MenuBundle\Document\MenuItem')
            ->will($this->returnValue($this->classMetadata));
        $this->classMetadata->expects($this->at(0))
            ->method('getIdentifierValue')
            ->will($this->returnValue('/this/is/content'));

        $this->assertEquals($this->expectedMenu, $this->normalizer->normalize($this->item));
    }

    public function testSupportsNormalization()
    {
        $res = $this->normalizer->supportsNormalization($this->item);
        $this->assertTrue($res);

        $res = $this->normalizer->supportsNormalization(new \StdClass);
        $this->assertFalse($res);
    }

    public function testDenormalize()
    {
        $this->dm->expects($this->at(0))
            ->method('find')
            ->with('Symfony\Cmf\Bundle\MenuBundle\Document\MenuItem', '/this/is/content')
            ->will($this->returnValue('test_content'));

        $rootItem = $this->normalizer->denormalize($this->expectedMenu, get_class($this->item));

        $this->assertNull($rootItem->getId());
        $this->assertEquals('0-item', $rootItem->getName());
        $this->assertEquals('Test', $rootItem->getLabel());
        $this->assertEquals('http://www.example.com', $rootItem->getUri());
        $this->assertEquals('test_route', $rootItem->getRoute());
        $this->assertEquals('test_content', $rootItem->getContent());
        $this->assertEquals(array('far' => 'boo'), $rootItem->getExtras());
        $this->assertEquals(array('bar' => 'foo'), $rootItem->getChildrenAttributes());
        $this->assertCount(1, $rootItem->getChildren());
    }

    public function testSupportsDenormalization()
    {
        $res = $this->normalizer->supportsDenormalization($this->expectedMenu, get_class($this->item));
        $this->assertTrue($res);

        $res = $this->normalizer->supportsDenormalization($this->expectedMenu, 'Bar');
        $this->assertFalse($res);
    }
}
