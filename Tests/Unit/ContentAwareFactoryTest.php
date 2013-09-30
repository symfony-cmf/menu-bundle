<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Unit;
use Symfony\Cmf\Bundle\MenuBundle\ContentAwareFactory;

class ContentAwareFactoryTest extends \PHPUnit_Framework_Testcase
{
    private $urlGenerator;
    private $contentUrlGenerator;
    private $securityContext;
    private $logger;

    private $node1;
    private $node2;
    private $node3;
    private $content;

    public function setUp()
    {
        $this->urlGenerator = $this->getMock(
            'Symfony\Component\Routing\Generator\UrlGeneratorInterface'
        );
        $this->contentUrlGenerator = $this->getMock(
            'Symfony\Component\Routing\Generator\UrlGeneratorInterface'
        );
        $this->securityContext = $this->getMock(
            'Symfony\Component\Security\Core\SecurityContextInterface'
        );
        $this->logger = $this->getMock(
            'Psr\Log\LoggerInterface'
        );

        $this->factory = new ContentAwareFactory(
            $this->urlGenerator,
            $this->contentUrlGenerator,
            $this->securityContext,
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

        $mock = $this->securityContext->expects($this->at(0))
            ->method('isGranted')
            ->with('VIEW', $this->node2)
        ;

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

        $this->securityContext->expects($this->at(1))
            ->method('isGranted')
            ->with('VIEW', $this->node3)
            ->will($this->returnValue(true))
        ;

        $res = $this->factory->createFromNode($this->node1);
        $this->assertInstanceOf('Knp\Menu\MenuItem', $res);
    }

    public function provideCreateItem()
    {
        return array(
            // route is used when type is route
            array('test', array(
                'uri' => 'foobar',
                'route' => 'testroute',
                'linkType' => 'route',
            )),

            // route is used when linkType ommitted and URI
            // not set.
            array('test', array(
                'route' => 'testroute',
            )),

            // content is used when linkType ommitted and URI
            // and route not set.
            array('test', array(
            ), array(
                'provideContent' => true,
            )),

            // content is used when linkType ommitted and URI
            // and route not set.
            array('test', array(
                'uri' => 'foobar',
                'route' => 'barfoo',
                'linkType' => 'content',
            ), array(
                'provideContent' => true,
            )),
        );
    }

    /**
     * @dataProvider provideCreateItem
     */
    public function testCreateItem($name, $options, $testOptions = array())
    {
        $options = array_merge(array(
            'content' => null,
            'routeParameters' => array(),
            'routeAbsolute' => false,
            'uri' => null,
            'route' => null,
            'linkType' => null,
        ), $options);

        $testOptions = array_merge(array(
            'allowEmptyItems' => false,
            'provideContent' => false,
        ), $testOptions);

        if (true === $testOptions['allowEmptyItems']) {
            $this->factory->setAllowEmptyItems(true);
        }

        if (true === $testOptions['provideContent']) {
            $options['content'] = $this->content;
        }

        $this->prepareCreateItemTests($name, $options);

        $item = $this->factory->createItem($name, $options);

        if (in_array($options['linkType'], array('uri', ''))) {
            $this->assertEquals($options['uri'], $item->getUri());
        }

        $this->assertEquals($name, $item->getName());
    }

    protected function prepareCreateItemTests($name, $options)
    {
        if (
            is_null($options['uri']) &&
            is_null($options['route']) &&
            is_null($options['content']) &&
            !in_array($options['linkType'], array(
                'route',
                'uri',
                'content',
                ''
            ))
        ) {
            $this->setExpectedException('\InvalidArgumentException');
        }

        if ($options['linkType'] == 'route') {
            $this->urlGenerator->expects($this->once())
                ->method('generate')
                ->with($options['route'], $options['routeParameters'], $options['routeAbsolute']);
        }

        if (
            null == $options['linkType'] &&
            empty($options['uri']) &&
            !empty($options['route'])
        ) {
            $this->urlGenerator->expects($this->once())
                ->method('generate')
                ->with($options['route'], $options['routeParameters'], $options['routeAbsolute']);
        }

        if ($options['linkType'] == 'content') {
            $this->contentUrlGenerator->expects($this->once())
                ->method('generate')
                ->with($options['content'], $options['routeParameters'], $options['routeAbsolute']);
        }

        if (
            null === $options['linkType'] &&
            empty($options['uri']) &&
            empty($options['route']) &&
            !empty($options['content'])
        ) {
            $this->contentUrlGenerator->expects($this->once())
                ->method('generate')
                ->with($options['content'], $options['routeParameters'], $options['routeAbsolute']);
        }
    }
}
