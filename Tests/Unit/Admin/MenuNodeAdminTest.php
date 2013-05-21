<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Unit\Admin;

use Symfony\Cmf\Bundle\MenuBundle\Admin\MenuNodeAdmin;

class MenuNodeAdminTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->admin = new MenuNodeAdmin('code', 'Class', 'SomeController', array('en'));
        $this->menuNode = $this->getMock('Symfony\Cmf\Bundle\MenuBundle\Document\MenuNode');
        $this->modelManager = $this->getMock('Sonata\AdminBundle\Model\ModelManagerInterface');
    }

    public function provideGetMenuForSubject()
    {
        return array(
            // invalid
            array('/cms/menu', '/cms/menu/main', array(
                'exception' => 'InvalidArgumentException',
            )),

            // valid
            array('/cms/menu', '/cms/menu/main/foobar', array(
                'find' => '/cms/menu/main',
            )),

            // valid
            array('/cms/menu', '/cms/menu/main/foobar/barfoo', array(
                'find' => '/cms/menu/main',
            )),
        );
    }

    /**
     * @dataProvider provideGetMenuForSubject
     */
    public function testGetMenuIdForSubject($basePath, $menuPath, $options)
    {
        $options = array_merge(array(
            'exception' => null,
            'find' => false,
        ), $options);

        $this->admin->setMenuRoot($basePath);
        $this->admin->setModelManager($this->modelManager);

        $this->menuNode->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($menuPath));

        if ($options['exception']) {
            $this->setExpectedException($options['exception']);
        }

        if ($options['find']) {
            $this->modelManager->expects($this->once())
                ->method('find')
                ->with(null, $options['find']);
        }

        $refl = new \ReflectionClass($this->admin);
        $method = $refl->getMethod('getMenuForSubject');
        $method->setAccessible(true);
        $res = $method->invokeArgs($this->admin, array($this->menuNode));
    }
}
