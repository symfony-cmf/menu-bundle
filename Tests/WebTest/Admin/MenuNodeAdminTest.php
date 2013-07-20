<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\WebTest\Admin\MenuNodeAdminTest;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class MenuNodeAdminTest extends BaseTestCase
{
    public function setUp()
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\DataFixtures\PHPCR\LoadMenuData',
        ));
        $this->client = $this->createClient();
    }

    public function testEdit()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/menu/menunode/test/test-menu/item1/edit');
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode());
    }
}
