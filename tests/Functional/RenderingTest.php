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

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Functional;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class RenderingTest extends BaseTestCase
{
    protected function setUp()
    {
        $this->db('PHPCR')->loadFixtures([
            'Symfony\Cmf\Bundle\MenuBundle\Tests\Fixtures\App\DataFixtures\PHPCR\LoadMenuData',
        ]);
    }

    public function testWithAutomaticLinkType()
    {
        $template = $this->getContainer()->get('twig')->createTemplate('{{ knp_menu_render("test-menu") }}');
        $dom = new \DOMDocument();
        $dom->loadXml($template->render([]));

        $items = [
            'item-1' => null,
            'This node has a URI' => 'http://www.example.com',
            'This node has content' => '/content-1',
            'This node has an assigned route' => '/link_test_route',
            'This node has an assigned route with parameters' => '/link_test_route/hello/foo/bar',
            'item-3' => null,
        ];

        $this->assertMenu($items, $dom);
    }

    public function testWithExplicitLinkType()
    {
        $template = $this->getContainer()->get('twig')->createTemplate('{{ knp_menu_render("another-menu") }}');
        $dom = new \DOMDocument();
        $dom->loadXml($template->render([]));

        $items = [
            'This node has uri, route and content set. but linkType is set to route' => '/link_test_route',
            'item-2' => null,
        ];

        $this->assertMenu($items, $dom);
    }

    protected function assertMenu($expectedItems, \DOMDocument $menu)
    {
        $xpath = new \DOMXpath($menu);
        $menuItems = [];
        foreach ($xpath->query('//ul/li/*[self::span or self::a]') as $menuItem) {
            $menuItems[$menuItem->textContent] = 'span' === $menuItem->nodeName
                ? null
                : $menuItem->getAttribute('href')
            ;
        }

        $this->assertEquals($expectedItems, $menuItems);
    }
}
