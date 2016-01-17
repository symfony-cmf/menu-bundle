<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Unit\Voter;

use Symfony\Cmf\Bundle\MenuBundle\Voter\UriPrefixVoter;

class UriPrefixVoterTest extends \PHPUnit_Framework_TestCase
{
    private $voter;
    private $request;

    protected function setUp()
    {
        $this->request = $this->prophesize('Symfony\Component\HttpFoundation\Request');
        $this->request->getLocale()->willReturn('');

        $this->voter = new UriPrefixVoter();
        $this->voter->setRequest($this->request->reveal());
    }

    public function testSkipsWhenNoContentIsAvailable()
    {
        $this->assertNull($this->voter->matchItem($this->createItem()));
    }

    public function testSkipsWhenNoRequestIsAvailable()
    {
        $this->voter->setRequest(null);

        $this->assertNull($this->voter->matchItem($this->createItem()));
    }

    public function testSkipsIfContentDoesNotExtendRoute()
    {
        $this->assertNull($this->voter->matchItem($this->createItem(new \stdClass())));
    }

    public function testSkipsIfContentHasNoCurrentUriPrefixOption()
    {
        $content = $this->prophesize('Symfony\Component\Routing\Route');
        $content->hasOption('currentUriPrefix')->willReturn(false);

        $this->assertNull($this->voter->matchItem($this->createItem($content->reveal())));
    }

    public function testMatchesCurrentUriPrefixOptionWithCurrentUri()
    {
        $content = $this->prophesize('Symfony\Component\Routing\Route');
        $content->hasOption('currentUriPrefix')->willReturn(true);
        $content->getOption('currentUriPrefix')->willReturn('/some/prefix');

        $this->request->getPathInfo()->willReturn('/some/prefix/page/12');

        $this->assertTrue($this->voter->matchItem($this->createItem($content->reveal())));
    }

    public function testSkipsWhenThereIsNoMatch()
    {
        $content = $this->prophesize('Symfony\Component\Routing\Route');
        $content->hasOption('currentUriPrefix')->willReturn(true);
        $content->getOption('currentUriPrefix')->willReturn('/some/prefix');

        $this->request->getPathInfo()->willReturn('/page/12');

        $this->assertNull($this->voter->matchItem($this->createItem($content->reveal())));
    }

    public function testReplacesSpecialLocalePlaceholderInCurrentUriPrefix()
    {
        $content = $this->prophesize('Symfony\Component\Routing\Route');
        $content->hasOption('currentUriPrefix')->willReturn(true);
        $content->getOption('currentUriPrefix')->willReturn('/{_locale}/prefix');

        $this->request->getPathInfo()->willReturn('/en/prefix/page/12');
        $this->request->getLocale()->willReturn('en');

        $this->assertTrue($this->voter->matchItem($this->createItem($content->reveal())));
    }

    private function createItem($content = null)
    {
        $item = $this->prophesize('Knp\Menu\ItemInterface');
        $item->getExtra('content')->willReturn($content);

        return $item->reveal();
    }
}
