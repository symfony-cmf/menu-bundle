<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Unit\Loader;

use Symfony\Cmf\Bundle\MenuBundle\Loader\VotingNodeLoader;

class VotingNodeLoaderTest extends \PHPUnit\Framework\TestCase
{
    private $subject;

    private $factory;

    private $dispatcher;

    public function setUp()
    {
        $this->factory = $this->createMock('Knp\Menu\FactoryInterface');
        $this->dispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->subject = new VotingNodeLoader($this->factory, $this->dispatcher);
    }

    /**
     * @dataProvider getCreateFromNodeData
     */
    public function testCreateFromNode($options)
    {
        // promises
        $node2 = $this->getNode('node2');
        $node3 = $this->getNode('node3');
        $node1 = $this->getNode('node1', [], [$node2, $node3]);

        // predictions
        $options = array_merge([
            'node2_is_published' => true,
        ], $options);

        $dispatchMethodMock = $this->dispatcher->expects($this->exactly(3))->method('dispatch');

        $nodes = 3;
        if (!$options['node2_is_published']) {
            $dispatchMethodMock->will($this->returnCallback(function ($name, $event) use ($node2) {
                if ($event->getNode() === $node2) {
                    $event->setSkipNode(true);
                }
            }));
            $nodes = 2;
        }

        $that = $this;
        $this->factory->expects($this->exactly($nodes))->method('createItem')->will($this->returnCallback(function () use ($that) {
            return $that->createMock('Knp\Menu\ItemInterface');
        }));

        // test
        $res = $this->subject->load($node1);
        $this->assertInstanceOf('Knp\Menu\ItemInterface', $res);
    }

    public function getCreateFromNodeData()
    {
        return [
            [[
            ]],
            [[
                'node2_is_published' => false,
            ]],
        ];
    }

    protected function getNode($name, $options = [], $children = [])
    {
        $node = $this->createMock('Knp\Menu\NodeInterface');

        $node->expects($this->any())->method('getName')->willReturn($name);
        $node->expects($this->any())->method('getOptions')->willReturn($options);
        $node->expects($this->any())->method('getChildren')->willReturn($children);

        return $node;
    }
}
