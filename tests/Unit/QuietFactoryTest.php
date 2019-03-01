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

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Unit;

use Symfony\Cmf\Bundle\MenuBundle\QuietFactory;

class QuietFactoryTest extends \PHPUnit\Framework\TestCase
{
    private $innerFactory;

    private $logger;

    protected function setUp()
    {
        $this->innerFactory = $this->prophesize('Knp\Menu\FactoryInterface');
        $this->logger = $this->prophesize('Psr\Log\LoggerInterface');
    }

    public function provideItemsWithNotExistingLinks()
    {
        return [
            [['route' => 'not_existent'], ['route' => 'not_existent']],
            [['content' => 'not_existent'], ['content' => 'not_existent']],
            [['linkType' => 'route', 'route' => 'not_existent'], ['linkType' => 'route']],
        ];
    }

    /** @dataProvider provideItemsWithNotExistingLinks */
    public function testAllowEmptyItemsReturnsItemWithoutURL(array $firstOptions, array $secondOptions)
    {
        $this->innerFactory->createItem('Home', $firstOptions)
            ->willThrow('Symfony\Component\Routing\Exception\RouteNotFoundException');

        $homeMenuItem = new \stdClass();
        $this->innerFactory->createItem('Home', $secondOptions)->willReturn($homeMenuItem);

        $factory = new QuietFactory($this->innerFactory->reveal(), $this->logger->reveal(), true);

        $this->assertEquals($homeMenuItem, $factory->createItem('Home', $firstOptions));
    }

    public function testDisallowEmptyItemsReturnsNull()
    {
        $this->innerFactory->createItem('Home', ['route' => 'not_existent'])
            ->willThrow('Symfony\Component\Routing\Exception\RouteNotFoundException');

        $factory = new QuietFactory($this->innerFactory->reveal(), $this->logger->reveal(), false);

        $this->assertNull($factory->createItem('Home', ['route' => 'not_existent']));
    }
}
