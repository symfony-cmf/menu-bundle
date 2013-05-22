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
        $crawler = $this->client->request('GET', '/admin/dashboard');
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode());
        $this->assertCount(1, $crawler->filter('html:contains("dashboard.label_menu_node")'));
    }
}
