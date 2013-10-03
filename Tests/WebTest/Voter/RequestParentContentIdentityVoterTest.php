<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\WebTest\Voter;

class RequestContentParentIdentityTest extends BaseTestCase
{
    public function testRequestContentParentIdentityNoOp()
    {
        // this test loads the "blog" page which corresponds directly 
        // to the "Request Content PArent Identity" menu item and so DOES NOT invoke
        // the voter.
        $crawler = $this->client->request('GET', '/blog');
        $this->assertCurrentItem($crawler, 'Request Parent Content Identity Voter');
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode());
    }

    public function testRequestContentParentIdentity()
    {
        // this test shows an post whose parent is the blog content referenced in teh menu item
        $crawler = $this->client->request('GET', '/blog/my-post');
        $res = $this->client->getResponse();
        $this->assertCurrentItem($crawler, 'Request Parent Content Identity Voter');
        $this->assertEquals(200, $res->getStatusCode());
    }
}

