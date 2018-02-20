<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Unit\Voter;

use Symfony\Cmf\Bundle\MenuBundle\Voter\RequestParentContentIdentityVoter;

class RequestParentContentIdentityVoterTest extends \PHPUnit_Framework_TestCase
{
    private $voter;

    private $request;

    protected function setUp()
    {
        $this->request = $this->prophesize('Symfony\Component\HttpFoundation\Request');

        $this->voter = new RequestParentContentIdentityVoter('_content', __CLASS__.'_ChildContent');
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

    public function testSkipsWhenNoContentAttributeWasDefined()
    {
        $attributes = $this->prophesize('Symfony\Component\HttpFoundation\ParameterBag');
        $attributes->has('_content')->willReturn(false);
        $this->request->attributes = $attributes;

        $this->assertNull($this->voter->matchItem($this->createItem(new \stdClass())));
    }

    public function testSkipsWhenContentObjectDoesNotImplementChildClass()
    {
        $attributes = $this->prophesize('Symfony\Component\HttpFoundation\ParameterBag');
        $attributes->has('_content')->willReturn(false);
        $this->request->attributes = $attributes;

        $this->assertNull($this->voter->matchItem($this->createItem(new \stdClass())));
    }

    public function testMatchesWhenParentContentIsEqualToCurrentContent()
    {
        $parent = new \stdClass();
        $content = new RequestParentContentIdentityVoterTest_ChildContent($parent);

        $attributes = $this->prophesize('Symfony\Component\HttpFoundation\ParameterBag');
        $attributes->has('_content')->willReturn(true);
        $attributes->get('_content')->willReturn($content);
        $this->request->attributes = $attributes;

        $this->assertTrue($this->voter->matchItem($this->createItem($parent)));
    }

    public function testSkipsWhenParentContentIsNotEqual()
    {
        $content = new RequestParentContentIdentityVoterTest_ChildContent(new \stdClass());

        $attributes = $this->prophesize('Symfony\Component\HttpFoundation\ParameterBag');
        $attributes->has('_content')->willReturn(true);
        $attributes->get('_content')->willReturn($content);
        $this->request->attributes = $attributes;

        $this->assertNull($this->voter->matchItem($this->createItem(new \stdClass())));
    }

    private function createItem($content = null)
    {
        $item = $this->prophesize('Knp\Menu\ItemInterface');
        $item->getExtra('content')->willReturn($content);

        return $item->reveal();
    }
}

class RequestParentContentIdentityVoterTest_ChildContent
{
    private $parent;

    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    public function getParentDocument()
    {
        return $this->parent;
    }
}
