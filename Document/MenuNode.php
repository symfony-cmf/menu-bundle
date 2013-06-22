<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Document;

use Knp\Menu\NodeInterface;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowInterface;

/**
 * This class represents a menu node for the cmf.
 *
 * @author Uwe Jäger <uwej711@googlemail.com>
 * @author Daniel Leech <daniel@dantleech.com>
 */
class MenuNode implements NodeInterface, PublishWorkflowInterface
{
    /**
     * Id of this menu node
     * @var string
     */
    protected $id;

    /**
     * Parent node
     * @var mixed
     */
    protected $parent;

    /**
     * Node name
     * @var string
     */
    protected $name;


    /**
     * Menu label
     * @var string
     */
    protected $label = '';

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var mixed
     */
    protected $weakContent;

    /**
     * @var mixed
     */
    protected $hardContent;

    /**
     * If we should use the weak or the strong
     * referened content.
     * @var boolean
     */
    protected $weak = true;

    /**
     * Attributes to add to the individual menu element
     * e.g. array('class' => 'foobar', 'style' => 'bar: foo')
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * Attribute to add to the children list element
     * e.g. array('class' => 'foobar', 'style' => 'bar: foo')
     *
     * @var array
     */
    protected $childrenAttributes = array();

    /**
     * Child menu nodes
     * @var MenuNode[]
     */
    protected $children = array();

    /**
     * Attributes to add to items link
     * e.g. array('class' => 'foobar', 'style' => 'bar: foo')
     *
     * @var array
     */
    protected $linkAttributes = array();

    /**
     * Attributes to add to the items label
     * e.g. array('class' => 'foobar', 'style' => 'bar: foo')
     *
     * @var array
     */
    protected $labelAttributes = array();

    /**
     * Hashmap for extra stuff associated to the node
     * @var array
     */
    protected $extras;

    /**
     * Parameters to use when generating the route
     * (for use with the "route" option)
     * @var array
     */
    protected $routeParameters = array();

    /**
     * @var boolean
     */
    protected $publishable = true;

    /**
     * @var \DateTime
     */
    protected $publishStartDate;

    /**
     * @var \DateTime
     */
    protected $publishEndDate;

    /**
     * Set to false to not render
     * @var boolean
     */
    protected $display = true;

    /**
     * Set to false to not render the children
     * @var boolean
     */
    protected $displayChildren = true;

    /**
     * Generate an absolute route
     * (to be used with "route" option)
     * @var boolean
     */
    protected $routeAbsolute = false;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    /**
     * Return ID (PHPCR path) of this menu node
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets ID (PHPCR path) of this menu node
     *
     * The recommended way is to use setParent and setName rather than setId.
     *
     * @param $id string
     *
     * @return MenuNode - this instance
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set the parent of this menu node
     *
     * @param $parent MenuNode - Parent node
     *
     * @return MenuNode - this instance
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Returns the parent of this menu node
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
     * Set the name of this node (used in ID)
     *
     * @param string $name
     *
     * @return MenuNode - this instance
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Convenience method to set parent and name at the same time.
     *
     * @param $parent MenuNode
     * @param $name string
     *
     * @return MenuNode - this instance
     */
    public function setPosition($parent, $name)
    {
        $this->parent = $parent;
        $this->name = $name;

        return $this;
    }

    /**
     * Return the label assigned to this menu node
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set label for this menu node
     *
     * @param $label string
     *
     * @return MenuNode - this instance
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
     * @return MenuNode - this instance
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
     * @return MenuNode - this instance
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Return the content document associated with this menu node
     *
     * @return object - ODM document
     */
    public function getContent()
    {
        if ($this->weak) {
            return $this->weakContent;
        }

        return $this->hardContent;
    }

    /**
     * Set the content document associated with this menu node
     *
     * NOTE: Content documents must be mapped by PHPCR-ODM so that it can be persisted.
     *
     * @param object $content
     *
     * @return MenuNode - this instance
     */
    public function setContent($content)
    {
        if ($this->weak) {
            $this->weakContent = $content;
        } else {
            $this->hardContent = $content;
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
     * @return MenuNode - this instance
     */
    public function setWeak($weak)
    {
        if ($this->weak && !$weak) {
            $this->hardContent = $this->weakContent;
            $this->weakContent = null;
        } elseif (!$this->weak && $weak) {
            $this->weakContent = $this->hardContent;
            $this->hardContent = null;
        }
        $this->weak = $weak;

        return $this;
    }

    /**
     * Return the attributes associated with this menu node
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set the attributes associated with this menu node
     *
     * @param $attributes array
     *
     * @return MenuNode - this instance
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
     * @return MenuNode - this instance
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Return the children attributes
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
     * @return MenuNode - this instance
     */
    public function setChildrenAttributes(array $attributes)
    {
        $this->childrenAttributes = $attributes;

        return $this;
    }

    /**
     * Get all child menu nodes of this menu node. This will filter out all
     * non-NodeInterface nodes.
     *
     * @return MenuNode[]
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
     * Gets the route parameters
     *
     * @return array
     */
    public function getRouteParameters()
    {
        return $this->routeParameters;
    }

    /**
     * Sets the route parameters
     *
     * @param array the parameters
     */
    public function setRouteParameters($routeParameters)
    {
        $this->routeParameters = $routeParameters;
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
            'display' => $this->display,
            'displayChildren' => $this->displayChildren,
            'content' => $this->getContent(),
            'routeParameters' => $this->getRouteParameters(),
            'routeAbsolute' => $this->routeAbsolute,
            'linkAttributes' => $this->linkAttributes,
            'labelAttributes' => $this->labelAttributes,

        );
    }

    /**
     * Get extra attributes
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
     * @return MenuNode - this instance
     */
    public function setExtras(array $extras)
    {
        $this->extras = $extras;

        return $this;
    }

    /**
     * Add a child menu node, automatically setting the parent node.
     *
     * @param MenuNode - Menu node to add
     *
     * @return MenuNode - The newly added child node.
     */
    public function addChild(MenuNode $child)
    {
        $child->setParent($this);
        $this->children[] = $child;

        return $child;
    }

    public function __toString()
    {
        return $this->getLabel() ? : '(no label set)';
    }

    public function isPublishable()
    {
        return $this->publishable;
    }

    public function setPublishable($publishable)
    {
        $this->publishable = $publishable;
    }

    public function getPublishStartDate()
    {
        return $this->publishStartDate;
    }

    public function setPublishStartDate(\DateTime $date = null)
    {
        $this->publishStartDate = $date;
    }

    public function getPublishEndDate()
    {
        return $this->publishEndDate;
    }

    public function setPublishEndDate(\DateTime $date = null)
    {
        $this->publishEndDate = $date;
    }

    public function getLinkAttributes() 
    {
        return $this->linkAttributes;
    }
    
    public function setLinkAttributes($linkAttributes)
    {
        $this->linkAttributes = $linkAttributes;
        return $this;
    }

    public function getLabelAttributes() 
    {
        return $this->labelAttributes;
    }
    
    public function setLabelAttributes($labelAttributes)
    {
        $this->labelAttributes = $labelAttributes;
        return $this;
    }

    public function getDisplay() 
    {
        return $this->display;
    }
    
    public function setDisplay($display)
    {
        $this->display = $display;
        return $this;
    }

    public function getDisplayChildren() 
    {
        return $this->displayChildren;
    }
    
    public function setDisplayChildren($displayChildren)
    {
        $this->displayChildren = $displayChildren;
        return $this;
    }

    public function getRouteAbsolute() 
    {
        return $this->routeAbsolute;
    }
    
    public function setRouteAbsolute($routeAbsolute)
    {
        $this->routeAbsolute = $routeAbsolute;
        return $this;
    }
}
