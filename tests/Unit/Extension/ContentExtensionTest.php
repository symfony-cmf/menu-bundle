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

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Unit\Extension;

use Symfony\Cmf\Bundle\MenuBundle\Extension\ContentExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ContentExtensionTest extends \PHPUnit\Framework\TestCase
{
    private $generator;

    private $subject;

    public function setUp()
    {
        $this->generator = $this->createMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $this->subject = new ContentExtension($this->generator);
    }

    public function getLinkTypeData()
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @dataProvider getLinkTypeData
     */
    public function testUriLinkType($typeSet)
    {
        $options = ['uri' => '/configured_uri'];
        if ($typeSet) {
            $options['linkType'] = 'uri';
        }

        $this->assertEquals(
            ['uri' => '/configured_uri', 'linkType' => 'uri', 'content' => null, 'extras' => ['content' => null]],
            $this->subject->buildOptions($options)
        );
    }

    /**
     * @dataProvider getLinkTypeData
     */
    public function testRouteLinkType($typeSet)
    {
        $options = ['route' => 'configured_route'];
        if ($typeSet) {
            $options['linkType'] = 'route';
        }

        $this->assertEquals(
            ['route' => 'configured_route', 'linkType' => 'route', 'content' => null, 'extras' => ['content' => null]],
            $this->subject->buildOptions($options)
        );
    }

    /**
     * @dataProvider getLinkTypeData
     */
    public function testContentLinkType($typeSet)
    {
        $options = ['content' => 'configured_content', 'routeParameters' => ['test' => 'foo'], 'routeAbsolute' => true];
        if ($typeSet) {
            $options['linkType'] = 'content';
        }

        $this->generator->expects($this->once())
                ->method('generate')
                ->with('configured_content', ['test' => 'foo'], UrlGeneratorInterface::ABSOLUTE_URL)
                ->willReturn('/generated_uri');

        $this->assertEquals(
            [
                'uri' => '/generated_uri',
                'linkType' => 'content',
                'content' => 'configured_content',
                'extras' => ['content' => 'configured_content'],
                'routeParameters' => ['test' => 'foo'],
                'routeAbsolute' => true,
            ],
            $this->subject->buildOptions($options)
        );
    }

    public function testOptionsAsRemovedWhenLinkTypeIsElse()
    {
        $options = [
            'uri' => '/configured_uri',
            'route' => 'configured_route',
            'content' => 'configured_content',
            'linkType' => 'content',
        ];

        $this->generator->expects($this->once())
            ->method('generate')
            ->with('configured_content', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/generated_uri');

        $this->assertEquals(
            [
                'uri' => '/generated_uri',
                'content' => 'configured_content',
                'linkType' => 'content',
                'extras' => ['content' => 'configured_content'],
            ],
            $this->subject->buildOptions($options)
        );
    }

    public function testFailsOnInvalidLinkType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid link type');

        $this->subject->buildOptions(['linkType' => 'not_valid']);
    }

    public function testFailsWhenContentIsNotAvailable()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('could not find content option');

        $this->subject->buildOptions(['linkType' => 'content']);
    }
}
