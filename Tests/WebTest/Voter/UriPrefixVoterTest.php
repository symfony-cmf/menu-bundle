<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\WebTest\Voter;

class UriPrefixVoterTest extends BaseTestCase
{
    public function testUriPrefixArticlesHomepage()
    {
        // this test loads the "articles" page which corresponds directly
        // to the "URI Prefix Voter" menu item and so DOES NOT invoke
        // the voter.
        $crawler = $this->client->request('GET', '/articles');

        $this->assertResponseSuccess($this->client->getResponse());
        $this->assertCurrentItem($crawler, 'URI Prefix Voter');
    }

    public function testUriPrefixArticle()
    {
        // this test shows an article which contains the prefix in the "/articles" route
        // as currentUriPrefix, and so the Voter IS used and the item should be selected.
        $crawler = $this->client->request('GET', '/articles/some-category/article-1');

        $this->assertResponseSuccess($this->client->getResponse());
        $this->assertCurrentItem($crawler, 'URI Prefix Voter');
    }
}
