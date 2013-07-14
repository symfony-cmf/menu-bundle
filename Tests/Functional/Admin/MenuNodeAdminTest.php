<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Functional\Admin\MenuNodeAdminTest;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class MenuNodeAdminTest extends BaseTestCase
{
    public function setUp()
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Symfony\Cmf\Bundle\MenuBundle\Tests\Functional\DataFixtures\PHPCR\LoadMenuData',
        ));
        $this->client = $this->createClient();
    }

    public function testDashboard()
    {
        $this->markTestIncomplete();
    }

    public function testEdit()
    {
        $crawler = $this->client->request('GET', '/admin/bundle/menu/menunode/test/test-menu/item1/edit');
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode());
    }
}
