<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\WebTest\Voter;

class RequestContentIdentityTest extends BaseTestCase
{
    public function testRequestContentIdentityVoterNoOp()
    {
        // this test will not invoke the content identity voter because
        // the URL of the content is the same as the URL for the menu item anyway
        // so it works by default
        $crawler = $this->client->request('GET', '/contents/content-1');
        $res = $this->client->getResponse();
        $this->assertCurrentItem($crawler, 'Request Content Identity Voter');
        $this->assertEquals(200, $res->getStatusCode());
    }

    public function testRequestContentIdentityVoter()
    {
        // this test DOES invoke the RequestContentIdentity voter because
        // the URL is different from that of the content, so if the menu item
        // is highlighted, it is because the voter is working.
        $crawler = $this->client->request('GET', '/cmi/request_content_identity');
        $res = $this->client->getResponse();
        $this->assertCurrentItem($crawler, 'Request Content Identity Voter');
        $this->assertEquals(200, $res->getStatusCode());
    }
}

