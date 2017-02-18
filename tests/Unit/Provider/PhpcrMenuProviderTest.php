<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Unit\Provider;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Loader\NodeLoader;
use Knp\Menu\NodeInterface;
use PHPCR\SessionInterface;
use Prophecy\Argument;
use Symfony\Cmf\Bundle\MenuBundle\Provider\PhpcrMenuProvider;

class PhpcrMenuProviderTest extends \PHPUnit_Framework_Testcase
{
    private $manager;

    public function setUp()
    {
        $this->manager = $this->prophesize(DocumentManagerInterface::class);
        $this->registry = $this->prophesize(ManagerRegistry::class);
        $this->document = $this->prophesize(NodeInterface::class);
        $this->item = $this->prophesize(ItemInterface::class);
        $this->nodeLoader = $this->prophesize(NodeLoader::class);
        $this->session = $this->prophesize(SessionInterface::class);

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
            ->willReturn(['jcr', 'nt']);

        $this->manager->find(Argument::cetera())->shouldNotBeCalled();

        $provider = $this->createProvider('/foo');

        $this->assertFalse($provider->has('notavalidnamespace:bar'));
        $this->assertFalse($provider->has('not:a:valid:name'));
    }

    public function provideMenuTests()
    {
        return [
            ['/test/menu', 'foo', '/test/menu/foo'],
            ['/test/menu', '/another/menu/path', '/another/menu/path'],
            ['/test/menu', 'jcr:namespaced', '/test/menu/jcr:namespaced'],
        ];
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
