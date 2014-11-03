<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
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
    private $logger;
    private $dispatcher;

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
        $this->logger = $this->getMock(
            'Psr\Log\LoggerInterface'
        );
        $this->dispatcher = $this->getMock(
            'Symfony\Component\EventDispatcher\EventDispatcherInterface'
        );

        $this->factory = new ContentAwareFactory(
            $this->urlGenerator,
            $this->contentUrlGenerator,
            $this->dispatcher,
            $this->logger
        );

        $this->node1 = $this->getMock('Knp\Menu\NodeInterface');
        $this->node2 = $this->getMock('Knp\Menu\NodeInterface');
        $this->node3 = $this->getMock('Knp\Menu\NodeInterface');

        $this->content = new \stdClass;
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
