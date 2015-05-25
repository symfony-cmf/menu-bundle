<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\WebTest\Render;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;
use Symfony\Component\DomCrawler\Crawler;

class TwigTest extends BaseTestCase
{
    public function setUp()
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\DataFixtures\PHPCR\LoadMenuData',
        ));
    }

    public function testTwig()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/render-test');
        $res = $client->getResponse();

        $this->assertEquals(200, $res->getStatusCode());
        $this->assertMenuHasItems($crawler->filter('#content ul')->eq(0), array(
            'This node has a URI',
            '@todo this node should have content',
            'This node has an assigned route',
            'This node has an assigned route with parameters',
            'item-3',
        ));
    }

    protected function assertMenuHasItems(Crawler $crawler, array $items)
    {
        foreach ($items as $item) {
            $this->assertCount(1, $crawler->filterXPath('//li/*[text()="'.$item.'"]'), 'Menu contains list item: '.$item);
        }
    }
}
