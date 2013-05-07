<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Functional\Admin\MenuNodeAdminTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MenuNodeAdminTest extends WebTestCase
{
    public function setUp()
    {
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
