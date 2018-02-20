<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Fixtures\App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCR;
use Knp\Menu\NodeInterface;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishableInterface;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNodeReferrersInterface;
use Symfony\Cmf\Component\Routing\RouteReferrersReadInterface;

/**
 * @PHPCR\Document(referenceable=true)
 */
class Content implements MenuNodeReferrersInterface, RouteReferrersReadInterface, PublishableInterface
{
    /**
     * @PHPCR\Id(strategy="assigned")
     */
    protected $id;

    /**
     * @PHPCR\Field(type="string")
     */
    protected $title;

    /**
     * @PHPCR\ParentDocument()
     */
    protected $parent;

    /**
     * @PHPCR\Nodename()
     */
    protected $name;

    /**
     * @PHPCR\Referrers(
     *     referringDocument="Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode",
     *     referencedBy="content",
     *     cascade="persist"
     * )
     */
    protected $menuNodes;

    /**
     * @PHPCR\Referrers(
     *     referringDocument="Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route",
     *     referencedBy="content"
     * )
     */
    protected $routes;

    private $published;

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

    public function setParentDocument($parent)
    {
        $this->parent = $parent;
    }

    public function getParentDocument()
    {
        return $this->parent;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isPublishable()
    {
        return $this->published;
    }

    /**
     * Set the boolean flag whether this content is publishable or not.
     *
     * @param bool $publishable
     */
    public function setPublishable($publishable)
    {
        $this->published = $publishable;
    }
}
