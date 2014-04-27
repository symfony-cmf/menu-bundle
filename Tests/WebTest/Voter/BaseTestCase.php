<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\WebTest\Voter;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase as BaseBaseTestCase;

abstract class BaseTestCase extends BaseBaseTestCase
{
    public function setUp()
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\DataFixtures\PHPCR\LoadMenuData',
        ));
        $this->client = $this->createClient();
    }

    protected function assertCurrentItem(Crawler $crawler, $title)
    {
        $res = $crawler->filter('li.current:contains("'.$title.'")')->count();
        $html = $crawler->html();

        $this->assertEquals(1, $res, 'Failed matching current menu item "'.$title.'", got '.$html);
    }
}
