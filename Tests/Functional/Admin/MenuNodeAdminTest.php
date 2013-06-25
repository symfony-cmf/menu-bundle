<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Functional\Admin\MenuNodeAdminTest;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class MenuNodeAdminTest extends BaseTestCase
{
    public function testDashboard()
    {
        $this->markTestIncomplete();
    }

    public function testEdit()
    {
        $crawler = $this->client->request('GET', '/admin/bundle/menu/menunode/test/testnode/edit');
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode());
    }
}
