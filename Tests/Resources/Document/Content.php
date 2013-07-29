<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNodeReferenceManyWriteInterface;
use Knp\Menu\NodeInterface;

/**
 * @PHPCRODM\Document(referenceable=true)
 */
class Content implements MenuNodeReferenceManyWriteInterface
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
        $this->menuNodes[] = $menuNode;
    }

    public function removeMenuNode(NodeInterface $tMenuNode)
    {
        $nodes = array();
        foreach ($this->menuNodes as $i => $menuNode) {
            if ($menuNode !== $tMenuNode) {
                $nodes[] = $menuNode;
            }
        }

        $this->menuNodes = $nodes;
    }
}
