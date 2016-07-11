<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Unit\Provider;

use Symfony\Cmf\Bundle\MenuBundle\Provider\PhpcrMenuProvider;
use Knp\Menu\Loader\NodeLoader;
use Symfony\Component\HttpFoundation\Request;
use Prophecy\Argument;

class PhpcrMenuProviderTest extends \PHPUnit_Framework_Testcase
{
    private $manager;
    private $registry;
    private $document;
    private $item;
    private $nodeLoader;
    private $session;

    public function setUp()
    {
        '[hello] world';
        $this->manager = $this->prophesize('Doctrine\ODM\PHPCR\DocumentManagerInterface');
        $this->registry = $this->prophesize('Doctrine\Common\Persistence\ManagerRegistry');
        $this->document = $this->prophesize('Knp\Menu\NodeInterface');
        $this->item = $this->prophesize('Knp\Menu\ItemInterface');
        $this->nodeLoader = $this->prophesize('Knp\Menu\Loader\NodeLoader');
        $this->session = $this->prophesize('PHPCR\SessionInterface');

        $this->manager->getPhpcrSession()->willReturn($this->session->reveal());
        $this->registry->getManager(null)->willReturn($this->manager->reveal());
    }

    /**
     *  @dataProvider provideMenuTests
     */
    public function testGet($menuRoot, $name, $expectedPath)
    {
        $this->manager->find(null, $expectedPath)
            ->willReturn($this->document->reveal());
        $this->nodeLoader->load($this->document->reveal())
            ->willReturn($this->item->reveal());

        $provider = $this->createProvider($menuRoot);
        $provider->setRequest(Request::create('/'));
        $item = $provider->get($name);

        $this->assertSame($this->item->reveal(), $item);
    }

    /**
     *  @dataProvider provideMenuTests
     */
    public function testHas($menuRoot, $name, $expectedPath)
    {
        $this->manager->find(null, $expectedPath)
            ->willReturn($this->document->reveal());

        $provider = $this->createProvider($menuRoot);
        $this->assertTrue($provider->has($name));
    }

    public function testHasNot()
    {
        $this->session->getNode()->shouldNotBeCalled();
        $this->session->getNamespacePrefixes()
            ->willReturn(array('jcr', 'nt'));

        $this->manager->find(Argument::cetera())->shouldNotBeCalled();

        $provider = $this->createProvider('/foo');

        $this->assertFalse($provider->has('notavalidnamespace:bar'));
        $this->assertFalse($provider->has('not:a:valid:name'));
    }

    public function provideMenuTests()
    {
        return array(
            array('/test/menu', 'foo', '/test/menu/foo'),
            array('/test/menu', '/another/menu/path', '/another/menu/path'),
            array('/test/menu', 'jcr:namespaced', '/test/menu/jcr:namespaced'),
        );
    }

    private function createProvider($basePath)
    {
        return new PhpcrMenuProvider(
            $this->nodeLoader->reveal(),
            $this->registry->reveal(),
            $basePath
        );
    }
}
