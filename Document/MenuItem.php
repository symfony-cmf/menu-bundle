<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Knp\Menu\NodeInterface;
use Knp\Menu\FactoryInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Cmf\Bundle\MenuBundle\Factory\MenuFactory;

/**
 * This class represents a menu item for the cmf.
 *
 * To protect against accidentally injecting things into the tree, all menu
 * item node names must end on -item.
 *
 * @author Uwe Jäger <uwej711@googlemail.com>
 * @author Daniel Leech <daniel@dantleech.com>
 *
 * @PHPCRODM\Document
 */
class MenuItem implements NodeInterface
{
    /**
     * Id of this menu item
     *
     * @PHPCRODM\Id
     */
    protected $id;

    /**
     * Parent node
     *
     * @PHPCRODM\ParentDocument
     */
    protected $parent;

    /**
     * Node name
     *
     * @PHPCRODM\Nodename
     */
    protected $name;

    /** @PHPCRODM\String */
    protected $label = '';

    /** @PHPCRODM\Uri */
    protected $uri;

    /** @PHPCRODM\String */
    protected $route;

    /** @PHPCRODM\ReferenceOne(strategy="weak") */
    protected $weakContent;

    /** @PHPCRODM\ReferenceOne(strategy="hard") */
    protected $strongContent;

    /** @PHPCRODM\Boolean */
    protected $weak = true;

    /** @PHPCRODM\String(multivalue=true, assoc="") */
    protected $attributes = array();

    /** @PHPCRODM\String(multivalue=true, assoc="") */
    protected $childrenAttributes = array();

    /** @PHPCRODM\Children() */
    protected $children = array();

    /** 
     * Hashmap for extra stuff associated to the item
     *
     * @PHPCRODM\String(assoc="") 
     */
    protected $extras;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Convenience method to set parent and name at the same time.
     */
    public function setPosition($parent, $name)
    {
        $this->parent = $parent;
        $this->name = $name;

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    public function getContent()
    {
        if ($this->weak) {
            return $this->weakContent;
        }

        return $this->strongContent;
    }

    public function setContent($content)
    {
        if ($this->weak) {
            $this->weakContent = $content;
        } else {
            $this->strongContent = $content;
        }

        return $this;
    }

    public function getWeak()
    {
        return $this->weak;
    }

    public function setWeak($weak)
    {
        if ($this->weak && !$weak) {
            $this->strongContent = $this->weakContent;
            $this->weakContent = null;
        } elseif (!$this->weak && $weak) {
            $this->weakContent = $this->strongContent;
            $this->strongContent = null;
        }
        $this->weak = $weak;

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param  string $name     The name of the attribute to return
     * @param  mixed  $default  The value to return if the attribute doesn't exist
     *
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        return $default;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    public function getChildrenAttributes()
    {
        return $this->childrenAttributes;
    }

    public function setChildrenAttributes(array $attributes)
    {
        $this->childrenAttributes = $attributes;

        return $this;
    }

    /**
     * Get all child menu items of this menu item. This will filter out all
     * non-NodeInterface items.
     *
     * @return array of NodeInterface
     */
    public function getChildren()
    {
        $children = array();
        foreach ($this->children as $child) {
            if (!$child instanceof NodeInterface) {
                continue;
            }
            $children[] = $child;
        }

        return $children;
    }

    public function getOptions()
    {
        return array(
            'uri' => $this->getUri(),
            'route' => $this->getRoute(),
            'label' => $this->getLabel(),
            'attributes' => $this->getAttributes(),
            'childrenAttributes' => $this->getChildrenAttributes(),
            'display' => true,
            'displayChildren' => true,
            'content' => $this->getContent(),
            // TODO provide the following information
            'routeParameters' => array(),
            'routeAbsolute' => false,
            'linkAttributes' => array(),
            'labelAttributes' => array(),
        );
    }

    public function getExtras()
    {
        return $this->extras;
    }

    public function setExtras(array $extras)
    {
        $this->extras = $extras;

        return $this;
    } 

    public function addChild($child)
    {
        if (!$child instanceof NodeInterface) {
            throw new \InvalidArgumentException('Cannot add menu item as child, it already belongs to another menu (e.g. has a parent).');
        }

        $child->setParent($this);
        $this->children[] = $child;

        return $child;
    }

    public function __toString()
    {
        return $this->getLabel();
    }
}
