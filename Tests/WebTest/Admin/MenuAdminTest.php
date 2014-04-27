<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\WebTest\Admin;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class MenuAdminTest extends BaseTestCase
{
    public function setUp()
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\DataFixtures\PHPCR\LoadMenuData',
        ));
        $this->client = $this->createClient();
    }

    public function testMenuList()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/menu/menu/list');
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode(), $res->getContent());
        $this->assertCount(1, $crawler->filter('html:contains("test-menu")'), $res->getContent());
    }

    public function testMenuEdit()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/menu/menu/test/menus/test-menu/edit');
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode(), $res->getContent());
        $this->assertCount(1, $crawler->filter('input[value="test-menu"]'), $res->getContent());
    }

    public function testMenuShow()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/menu/menu/test/menus/test-menu/show');
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode(), $res->getContent());
        $this->assertCount(2, $crawler->filter('td:contains("test-menu")'), $res->getContent());
    }

    public function testMenuCreate()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/menu/menu/create');
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode(), $res->getContent());

        $button = $crawler->selectButton('Create');
        $form = $button->form();
        $node = $form->getFormNode();
        $actionUrl = $node->getAttribute('action');
        $uniqId = substr(strchr($actionUrl, '='), 1);

        $form[$uniqId.'[name]'] = 'foo-test';
        $form[$uniqId.'[label]'] = 'Foo Test';

        $this->client->submit($form);
        $res = $this->client->getResponse();

        // If we have a 302 redirect, then all is well
        $this->assertEquals(302, $res->getStatusCode(), $res->getContent());
    }
}
