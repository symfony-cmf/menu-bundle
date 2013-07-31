<?php

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
     * @PHPCRODM\Id()
     */
    protected $id;

    /**
     * @PHPCRODM\String()
     */
    protected $title;

    /**
     * @PHPCRODM\Referrers(
     *     referringDocument="Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode",
     *     referencedBy="content",
     *     cascade="persist"
     * )
     */
    protected $menuNodes;

    public function __construct()
    {
        $this->menuNodes = new ArrayCollection();
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

    public function removeMenuNode(NodeInterface $menuNode)
    {
        $this->menuNodes->remove($menuNode);
    }

    public function getRoutes()
    {
        return array(new Route('http://www.example.com/content'));
    }
}
