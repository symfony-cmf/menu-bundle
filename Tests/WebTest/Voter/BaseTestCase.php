<?php

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
        if (method_exists($crawler, 'html')) {
            // since symfony 2.3
            $html = $crawler->html();
        } else {
            // symfony 2.2
            $html = '';
            foreach ($crawler as $domElement) {
                $html .= $domElement->ownerDocument->saveHTML($domElement);
            }

        }
        $this->assertEquals(1, $res, 'Failed matching current menu item "'.$title.'", got '.$html);
    }
}
