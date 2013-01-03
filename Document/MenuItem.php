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

    /**
     * Return ID (path) of this menu item
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets ID (path) of this menu item
     *
     * @param $id string
     *
     * @return MenuItem - this instance
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set the parent of this menu item
     *
     * @param $parent MenuItem - Parent item
     *
     * @return MenuItem - this instance
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Returns the parent of this menu item
     *
     * @return object
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of this item (used in ID)
     *
     * @param string $name
     *
     * @return MenuItem - this instance
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Convenience method to set parent and name at the same time.
     *
     * @param $parent MenuItem
     * @param $name string
     *
     * @return MenuItem - this instance
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

    /**
     * Set label for this menu item
     *
     * @param $label string
     *
     * @return MenuItem - this instance
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Return the URI
     *
     * @return $uri string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set the URI
     *
     * @param $uri string
     *
     * @return MenuItem - this instance
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Return the route name
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set the route name
     *
     * @param $route string - name of route
     *
     * @return MenuItem - this instance
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Return the content document associcated with this menu item
     *
     * @return object - ODM document
     */
    public function getContent()
    {
        if ($this->weak) {
            return $this->weakContent;
        }

        return $this->strongContent;
    }

    /**
     * Set the content document associated with this menu item
     *
     * @param object $content
     *
     * @return MenuItem - this instance
     */
    public function setContent($content)
    {
        if ($this->weak) {
            $this->weakContent = $content;
        } else {
            $this->strongContent = $content;
        }

        return $this;
    }

    /**
     * Return true if this the content is referenced weakly.
     *
     * @return boolean
     */
    public function getWeak()
    {
        return $this->weak;
    }

    /**
     * Specify if the content should be referenced weakly.
     *
     * @param $weak boolean
     *
     * @return MenuItem - this instance
     */
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

    /**
     * Return the attributes associated with this menu item
     *
     * @notsure: What do these attributes apply to are they generic?
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set the attributes associcated with this menu item
     *
     * @param $attributes array
     *
     * @return MenuItem - this instance
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Return the given attribute, optionally specifying a default value
     *
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

    /**
     * Set the named attribute
     *
     * @param $name string - attribute name
     * @param $value mixed - attribute value
     *
     * @return MenuItem - this instance
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Return the children attributes
     *
     * @notsure: What does this apply to?
     *
     * @return array
     */
    public function getChildrenAttributes()
    {
        return $this->childrenAttributes;
    }

    /**
     * Set the children attributes
     *
     * @param $attributes array
     *
     * @return MenuItem - this instance
     */
    public function setChildrenAttributes(array $attributes)
    {
        $this->childrenAttributes = $attributes;

        return $this;
    }

    /**
     * Get all child menu items of this menu item. This will filter out all
     * non-NodeInterface items.
     *
     * @return MenuItem[]
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

    /**
     * {@inheritDoc}
     */
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

    /**
     * Get extra attributes
     *
     * @notsure: How is this different from getAttributes?
     *
     * @return array
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * Set the extra attributes
     *
     * @param $extras array
     *
     * @return MenuItem - this instance
     */
    public function setExtras(array $extras)
    {
        $this->extras = $extras;

        return $this;
    } 

    /**
     * Add a child menu item, automatically setting the parent node.
     *
     * @param MenuItem - Menu item to add
     *
     * @return MenuItem - Same item.
     */
    public function addChild($child)
    {
        if (!$child instanceof MenuItem) {
            throw new \InvalidArgumentException('Cannot add menu item, it is not an instance of MenuItem.');
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
