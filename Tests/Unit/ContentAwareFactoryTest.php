<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests;
use Symfony\Cmf\Bundle\MenuBundle\Document\MenuNode;
use Symfony\Cmf\Bundle\MenuBundle\ContentAwareFactory;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class ContentAwareFactoryTest extends \PHPUnit_Framework_Testcase
{
    public function setUp()
    {
        $this->pwfc = $this->getMock(
            'Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowCheckerInterface'
        );
        $this->urlGenerator = $this->getMock(
            'Symfony\Component\Routing\Generator\UrlGeneratorInterface'
        );
        $this->contentUrlGenerator = $this->getMock(
            'Symfony\Component\Routing\Generator\UrlGeneratorInterface'
        );
        $this->logger = $this->getMock(
            'Psr\Log\LoggerInterface'
        );

        $this->factory = new ContentAwareFactory(
            $this->urlGenerator,
            $this->contentUrlGenerator,
            $this->pwfc,
            $this->logger,
            false // refactore this empty items option
        );

        $this->node1 = $this->getMock('Knp\Menu\NodeInterface');
        $this->node2 = $this->getMock('Knp\Menu\NodeInterface');
        $this->node3 = $this->getMock('Knp\Menu\NodeInterface');

        $this->content = new \stdClass;
    }

    public function provideCreateFromNode()
    {
        return array(
            array(array(
            )),
            array(array(
                'node2_is_published' => false,
            )),
        );
    }

    /**
     * @dataProvider provideCreateFromNode
     */
    public function testCreateFromNode($options)
    {
        $options = array_merge(array(
            'node2_is_published' => true
        ), $options);

        $this->contentUrlGenerator->expects($this->any())
            ->method('generate')
            ->will($this->returnValue('foobar'));

        $this->node1->expects($this->once())
            ->method('getOptions')->will($this->returnValue(array()));
        $this->node3->expects($this->once())
            ->method('getOptions')->will($this->returnValue(array()));

        $this->node1->expects($this->once())
            ->method('getChildren')
            ->will($this->returnValue(array(
                $this->node2,
                $this->node3,
            )));

        $this->node3->expects($this->once())
            ->method('getChildren')
            ->will($this->returnValue(array()));

        $mock = $this->pwfc->expects($this->at(0))
            ->method('checkIsPublished')
            ->with($this->node2);

        if ($options['node2_is_published']) {
            $mock->will($this->returnValue(true));
            $this->node2->expects($this->once())
                ->method('getOptions')->will($this->returnValue(array()));
            $this->node2->expects($this->once())
                ->method('getChildren')
                ->will($this->returnValue(array()));
        } else {
            $mock->will($this->returnValue(false));
        }

        $this->pwfc->expects($this->at(1))
            ->method('checkIsPublished')
            ->with($this->node3);

        $res = $this->factory->createFromNode($this->node1);
        $this->assertInstanceOf('Knp\Menu\MenuItem', $res);
    }

    public function provideCreateItem()
    {
        return array(
            array(array(
                'allow_empty_items' => false,

                'has_content_route' => true,
                'content_found' => false,
            )),

            array(array(
                'allow_empty_items' => true,

                'has_content_route' => true,
                'content_found' => false,
            )),

            array(array(
                'has_content_route' => true,
                'content_found' => true,
            )),
        );
    }

    /**
     * @dataProvider provideCreateItem
     */
    public function testCreateItem($options)
    {
        $options = array_merge(array(
            'allow_empty_items' => false,

            'has_content_route' => false,
            'content_found' => false,
        ));

        $content = new \stdClass;

        if ($options['has_content_route']) {
            if (!$options['content_found']) {
                $this->contentUrlGenerator->expects($this->once())
                    ->method('generate')
                    ->will($this->throwException(new RouteNotFoundException('test')));
            }
        }

        $this->factory->setAllowEmptyItems($options['allow_empty_items']);
        $res = $this->factory->createItem('foobar', array());

        if (true === $options['has_content_route']) {
            if (false === $options['content_found']) {
                if ($options['allow_empty_items']) {
                    $this->assertNull($res);
                    return;
                }
            }        
        }

        $this->assertInstanceOf('\Knp\Menu\MenuItem', $res);
    }
}
