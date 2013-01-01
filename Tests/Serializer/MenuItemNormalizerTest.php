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
    }

    public function testNormalize()
    {
        $this->dm->expects($this->exactly(2))
            ->method('getClassMetadata')
            ->will($this->returnValue($this->classMetadata));
        $this->classMetadata->expects($this->at(0))
            ->method('getIdentifierValue')
            ->will($this->returnValue('/this/is/content'));
        $expected = array (
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

        $this->assertEquals($expected, $this->normalizer->normalize($this->item));
    }
}
