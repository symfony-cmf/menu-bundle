<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

use Knp\Menu\NodeInterface;

use Symfony\Component\Routing\Route;
use Symfony\Cmf\Component\Routing\RouteReferrersReadInterface;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNodeReferrersInterface;

/**
 * @PHPCRODM\Document(referenceable=true)
 */
class Content implements MenuNodeReferrersInterface, RouteReferrersReadInterface
{
    /**
     * @PHPCRODM\Id(strategy="assigned")
     */
    protected $id;

    /**
     * @PHPCRODM\String()
     */
    protected $title;

    /**
     * @PHPCRODM\ParentDocument()
     */
    protected $parent;

    /**
     * @PHPCRODM\Referrers(
     *     referringDocument="Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode",
     *     referencedBy="content",
     *     cascade="persist"
     * )
     */
    protected $menuNodes;

    /**
     * @PHPCRODM\Referrers(
     *     referringDocument="Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route",
     *     referencedBy="content"
     * )
     */
    protected $routes;

    public function __construct()
    {
        $this->menuNodes = new ArrayCollection();
        $this->routes = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getMenuNodes()
    {
        return $this->menuNodes;
    }

    public function addMenuNode(NodeInterface $menuNode)
    {
        $this->menuNodes->add($menuNode);
    }

    public function addRoute($route)
    {
        $this->routes->add($route);
    }


    public function removeMenuNode(NodeInterface $menuNode)
    {
        $this->menuNodes->remove($menuNode);
    }

    public function getRoutes()
    {
        foreach ($this->routes as $route) {
        }
        return $this->routes;
    }

    public function getParent()
    {
        return $this->parent;
    }
}
