<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Knp\Menu\MenuItem as BaseMenuItem;
use Knp\Menu\ItemInterface;
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
 *
 * @PHPCRODM\Document
 */
class MenuItem extends BaseMenuItem
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

    /** @PHPCRODM\String(multivalue=true, assoc="") */
    protected $linkAttributes = array();

    /** 
     * Hashmap for extra stuff associated to the item
     *
     * @PHPCRODM\String(assoc="") 
     */
    protected $extras;

    protected $factory;

    public function __construct($name = null, MenuFactory $factory = null)
    {
        $this->name = $name;
        $this->factory = $factory;
    }

    public function setFactory(FactoryInterface $factory)
    {
        $this->factory = $factory;
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

    /**
     * Convenience method to set parent and name at the same time.
     */
    public function setPosition($parent, $name)
    {
        $this->parent = $parent;
        $this->name = $name;

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
            if (!$child instanceof ItemInterface) {
                continue;
            }
            $children[] = $child;
        }

        return $children;
    }

    // @QUESTION: Should this be removed in favor of ->toArray ?
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

    public function __toString()
    {
        return $this->getLabel();
    }

    public function toArray($depth = null)
    {
        $array = array(
            'id' => $this->id,
            'parent' => (string) $this->parent,
            'name' => $this->name,
            'label' => $this->label,
            'uri' => $this->uri,
            'route' => $this->route,
            'content' => $this->weak ? (string) $this->weakContent : (string) $this->strongContent,
            'weak' => $this->weak,
            'attributes' => $this->attributes,
            'extras' => $this->extras,
            'childrenAttributes' => $this->childrenAttributes,
        );

        // export the children as well, unless explicitly disabled
        if (0 !== $depth) {
            $childDepth = (null === $depth) ? null : $depth - 1;
            $array['children'] = array();
            if (null !== $this->children) {
                foreach ($this->children as $key => $child) {
                    $array['children'][$key] = $child->toArray($childDepth);
                }
            }
        }

        return $array;
    }

    /**
     * Add a child menu item to this menu
     *
     * Returns the child item
     *
     * @param mixed $child   An ItemInterface instance or the name of a new item to create
     * @param array $options If creating a new item, the options passed to the factory for the item
     * @return \Knp\Menu\ItemInterface
     */
    public function addChild($child, array $options = array())
    {
        if (!$child instanceof ItemInterface) {
            $child = $this->factory->createItem($child, $options);
        } elseif (null !== $child->getParent()) {
            throw new \InvalidArgumentException('Cannot add menu item as child, it already belongs to another menu (e.g. has a parent).');
        }

        $child->setParent($this);
        $child->setCurrentUri($this->getCurrentUri());

        $this->children[] = $child;

        return $child;
    }

    /**
     * Returns the child menu identified by the given name
     *
     * @param  string $name  Then name of the child menu to return
     * @return \Knp\Menu\ItemInterface|null
     */
    public function getChild($name)
    {
        foreach ($this->children as $child) {
            if ($child->getName() == $name) {
                return $child;
            }
        }

        return null;
    }
}
