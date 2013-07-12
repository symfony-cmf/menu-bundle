<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\WebTest\Render;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class TwigTest extends BaseTestCase
{
    public function setUp()
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\DataFixtures\PHPCR\LoadMenuData',
        ));
        $this->client = $this->createClient();
    }

    public function testTwig()
    {
        $crawler = $this->client->request('GET', '/render-test');
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode());
    }
}

