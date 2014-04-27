<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Unit\Provider;

use Symfony\Cmf\Bundle\MenuBundle\Provider\PhpcrMenuProvider;

class PhpcrMenuProviderTest extends \PHPUnit_Framework_Testcase
{
    private function getDmMock($path)
    {
        $session = $this->getMock('PHPCR\SessionInterface');
        $session->expects($this->once())
            ->method('getNode')
            ->with($path)
        ;
        $dm = $this
            ->getMockBuilder('Doctrine\ODM\PHPCR\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $dm->expects($this->once())
            ->method('getPhpcrSession')
            ->will($this->returnValue($session))
        ;

        return $dm;
    }

    /**
     *  @dataProvider getMenuTests
     */
    public function testGet($menuRoot, $name, $expectedPath)
    {
        $objectManager = $this->getDmMock($expectedPath);
        $objectManager->expects($this->once())
            ->method('find')
            ->with($this->equalTo(null), $this->equalTo($expectedPath))
            ->will($this->returnValue($this->getMock('Knp\Menu\NodeInterface')))
        ;

        $managerRegistry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $managerRegistry->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($objectManager));

        $factory = $this->getMock('Knp\Menu\FactoryInterface');
        $factory->expects($this->once())
            ->method('createFromNode')
            ->will($this->returnValue($this->getMock('Knp\Menu\ItemInterface')));

        $provider = new PhpcrMenuProvider(
            $factory,
            $managerRegistry,
            $menuRoot
        );

        $provider->setRequest($this->getMock('Symfony\Component\HttpFoundation\Request'));

        $provider->get($name);
    }

    /**
     *  @dataProvider getMenuTests
     */
    public function testHas($menuRoot, $name, $expectedPath)
    {
        $objectManager = $this->getDmMock($expectedPath);
        $objectManager->expects($this->once())
            ->method('find')
            ->with($this->equalTo(null), $this->equalTo($expectedPath))
            ->will($this->returnValue($this->getMock('Knp\Menu\NodeInterface')));

        $managerRegistry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $managerRegistry->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($objectManager));

        $provider = new PhpcrMenuProvider(
            $this->getMock('Knp\Menu\FactoryInterface'),
            $managerRegistry,
            $menuRoot
        );

        $provider->has($name);
    }

    public function getMenuTests()
    {
        return array(
            array('/test/menu', 'foo', '/test/menu/foo'),
            array('/test/menu', '/another/menu/path', '/another/menu/path'),
        );
    }

}
