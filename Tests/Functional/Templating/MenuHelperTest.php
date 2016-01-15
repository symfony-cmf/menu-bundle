<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Functional\Templating;

use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNodeReferrersInterface;
use Symfony\Cmf\Bundle\MenuBundle\Templating\MenuHelper;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;
use Knp\Menu\NodeInterface;

class MenuHelperTest extends BaseTestCase
{
    private $helper;

    protected function setUp()
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\DataFixtures\PHPCR\LoadMenuData',
        ));

        $container = $this->getContainer();
        $this->helper = new MenuHelper($container->get('doctrine_phpcr'), $container->get('knp_menu.factory'));
    }

    /**
     * @dataProvider provideGetBreadcrumbArrayData
     */
    public function testGetBreadcrumbArray($includeMenuRoot)
    {
        $currentNode = $this->db('PHPCR')->getOm()->find(null, '/test/menus/test-menu/item-2/sub-item-2');

        $breadcrumbs = $this->helper->getBreadcrumbArray($currentNode, $includeMenuRoot);

        // simplify the returned breadcrumb array
        $breadcrumbs = array_map(function ($breadcrumb) {
            return array('uri' => $breadcrumb['uri'], 'item_name' => $breadcrumb['item']->getName());
        }, $breadcrumbs);

        $expectedBreadcrumbs = array_merge(
            $includeMenuRoot ? array(array('uri' => null, 'item_name' => 'test-menu')) : array(),
            array(
                array('uri' => 'http://www.example.com', 'item_name' => 'item-2'),
                array('uri' => '/link_test_route', 'item_name' => 'sub-item-2'),
            )
        );

        $this->assertEquals($expectedBreadcrumbs, $breadcrumbs);
    }

    public function provideGetBreadcrumbArrayData()
    {
        return array('menu root included' => array(true), 'menu route excluded' => array(false));
    }

    /**
     * @dataProvider provideGetCurrentNodeWithRouteData
     */
    public function testGetCurrentNodeWithRoute($routeName, $nodeName)
    {
        $attributes = $this->prophesize('Symfony\Component\HttpFoundation\ParameterBag');
        $attributes->has(RouteObjectInterface::CONTENT_OBJECT)->willReturn(false);
        $attributes->has(RouteObjectInterface::ROUTE_NAME)->willReturn(true);
        $attributes->get(RouteObjectInterface::ROUTE_NAME)->willReturn($routeName);

        $request = $this->prophesize('Symfony\Component\HttpFoundation\Request');
        $request->attributes = $attributes->reveal();

        $node = $this->helper->getCurrentNode($request->reveal());
        $this->assertInstanceOf('Knp\Menu\NodeInterface', $node);
        $this->assertEquals($nodeName, $node->getName());
    }

    public function provideGetCurrentNodeWithRouteData()
    {
        return array(
            'simple route refering node' => array('link_test_route_with_params', 'sub-item-3'),
            'multiple matching nodes' => array('link_test_route', 'item-1'),
        );
    }

    public function testGetCurrentNodeWithContent()
    {
        $content = new MenuHelperTest_NodeReferrer();
        $content->addMenuNode($this->db('PHPCR')->getOm()->find(null, '/test/menus/test-menu/item-1'));

        $attributes = $this->prophesize('Symfony\Component\HttpFoundation\ParameterBag');
        $attributes->has(RouteObjectInterface::CONTENT_OBJECT)->willReturn(true);
        $attributes->has(RouteObjectInterface::ROUTE_NAME)->willReturn(true);
        $attributes->get(RouteObjectInterface::CONTENT_OBJECT)->willReturn($content);

        $request = $this->prophesize('Symfony\Component\HttpFoundation\Request');
        $request->attributes = $attributes->reveal();

        $node = $this->helper->getCurrentNode($request->reveal());
        $this->assertInstanceOf('Knp\Menu\NodeInterface', $node);
        $this->assertEquals('item-1', $node->getName());
    }

    public function testGetCurrentNodeWithoutMatch()
    {
        $attributes = $this->prophesize('Symfony\Component\HttpFoundation\ParameterBag');
        $attributes->has(RouteObjectInterface::CONTENT_OBJECT)->willReturn(false);
        $attributes->has(RouteObjectInterface::ROUTE_NAME)->willReturn(false);

        $request = $this->prophesize('Symfony\Component\HttpFoundation\Request');
        $request->attributes = $attributes->reveal();

        $this->assertEquals(null, $this->helper->getCurrentNode($request->reveal()));
    }

    public function testGetCurrentItemWithMatch()
    {
        $attributes = $this->prophesize('Symfony\Component\HttpFoundation\ParameterBag');
        $attributes->has(RouteObjectInterface::CONTENT_OBJECT)->willReturn(false);
        $attributes->has(RouteObjectInterface::ROUTE_NAME)->willReturn(true);
        $attributes->get(RouteObjectInterface::ROUTE_NAME)->willReturn('link_test_route_with_params');

        $request = $this->prophesize('Symfony\Component\HttpFoundation\Request');
        $request->attributes = $attributes->reveal();

        $item = $this->helper->getCurrentItem($request->reveal());
        $this->assertInstanceOf('Knp\Menu\ItemInterface', $item);
        $this->assertEquals('sub-item-3', $item->getName());
    }
}

class MenuHelperTest_NodeReferrer implements MenuNodeReferrersInterface
{
    private $nodes = array();

    public function getMenuNodes()
    {
        return $this->nodes;
    }

    public function addMenuNode(NodeInterface $menu)
    {
        $this->nodes[] = $menu;
    }

    public function removeMenuNode(NodeInterface $menu)
    {
        // dummy
    }
}
