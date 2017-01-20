<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr;

use Doctrine\ODM\PHPCR\HierarchyInterface;
use Knp\Menu\NodeInterface;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNode as ModelMenuNode;

class MenuNode extends ModelMenuNode implements HierarchyInterface
{
    /**
     * Set the parent of this menu node.
     *
     * @param object $parent A mapped document
     *
     * @return MenuNode - this instance
     */
    public function setParentDocument($parent)
    {
        return $this->setParentObject($parent);
    }

    /**
     * Returns the parent of this menu node.
     *
     * @return object
     */
    public function getParentDocument()
    {
        return $this->getParentObject();
    }

    /**
     * @deprecated For BC with the PHPCR-ODM 1.4 HierarchyInterface
     * @see setParentDocument
     */
    public function setParent($parent)
    {
        @trigger_error('The '.__METHOD__.'() method is deprecated and will be removed in version 3.0. Use setParentDocument() instead.', E_USER_DEPRECATED);

        return $this->setParentDocument($parent);
    }

    /**
     * @deprecated For BC with the PHPCR-ODM 1.4 HierarchyInterface
     * @see getParentDocument
     */
    public function getParent()
    {
        @trigger_error('The '.__METHOD__.'() method is deprecated and will be removed in version 3.0. Use getParentDocument() instead.', E_USER_DEPRECATED);

        return $this->getParentDocument();
    }

    /**
     * Convenience method to set parent and name at the same time.
     *
     * @param object $parent A mapped document
     * @param string $name
     *
     * @return MenuNode - this instance
     */
    public function setPosition($parent, $name)
    {
        $this->setParentObject($parent);
        $this->setName($name);

        return $this;
    }

    /**
     * Add a child menu node, automatically setting the parent node.
     *
     * @param NodeInterface $child
     *
     * @return NodeInterface - The newly added child node
     */
    public function addChild(NodeInterface $child)
    {
        if ($child instanceof self) {
            $child->setParentObject($this);
        }

        return parent::addChild($child);
    }
}
