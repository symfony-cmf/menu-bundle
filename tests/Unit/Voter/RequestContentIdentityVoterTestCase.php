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

use Knp\Menu\ItemInterface;
use Symfony\Cmf\Bundle\MenuBundle\Voter\RequestContentIdentityVoter;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

abstract class RequestContentIdentityVoterTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequestContentIdentityVoter
     */
    protected $voter;

    /**
     * @var Request
     */
    protected $request;

    protected function setUp()
    {
        $this->request = $this->prophesize(Request::class);

        $this->voter = $this->buildVoter($this->request->reveal());
    }

    abstract protected function buildVoter(Request $request);

    abstract public function testSkipsWhenNoRequestIsAvailable();

    public function testSkipsWhenNoContentIsAvailable()
    {
        $this->assertNull($this->voter->matchItem($this->createItem()));
    }

    public function testSkipsWhenNoContentAttributeWasDefined()
    {
        $attributes = $this->prophesize(ParameterBag::class);
        $attributes->has('_content')->willReturn(false);
        $this->request->attributes = $attributes;

        $this->assertNull($this->voter->matchItem($this->createItem(new \stdClass())));
    }

    public function testMatchesWhenContentIsEqualToCurrentContent()
    {
        $content = new \stdClass();

        $attributes = $this->prophesize(ParameterBag::class);
        $attributes->has('_content')->willReturn(true);
        $attributes->get('_content')->willReturn($content);
        $this->request->attributes = $attributes;

        $this->assertTrue($this->voter->matchItem($this->createItem($content)));
    }

    public function testSkipsWhenContentIsNotEqual()
    {
        $attributes = $this->prophesize(ParameterBag::class);
        $attributes->has('_content')->willReturn(true);
        $attributes->get('_content')->willReturn(new \stdClass());
        $this->request->attributes = $attributes;

        $this->assertNull($this->voter->matchItem($this->createItem(new \stdClass())));
    }

    protected function createItem($content = null)
    {
        $item = $this->prophesize(ItemInterface::class);
        $item->getExtra('content')->willReturn($content);

        return $item->reveal();
    }
}
