<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Knp\Menu\NodeInterface;

/**
 * This is a persistable implementation of the KnpMenu
 * NodeInterface.
 *
 * @author Uwe Jäger <uwej711@googlemail.com>
 * @author Daniel Leech <daniel@dantleech.com>
 */
class MenuNodeBase implements NodeInterface
{
    /**
     * Id of this menu node.
     *
     * @var string
     */
    protected $id;

    /**
     * Node name.
     *
     * @var string
     */
    protected $name;

    /**
     * Child menu nodes.
     *
     * @var Collection
     */
    protected $children;

    /**
     * Menu label.
     *
     * @var string
     */
    protected $label = '';

    /**
     * @var string
     */
    protected $uri;

    /**
     * The name of the route to generate.
     *
     * @var string
     */
    protected $route;

    /**
     * HTML attributes to add to the individual menu element.
     *
     * e.g. array('class' => 'foobar', 'style' => 'bar: foo')
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * HTML attributes to add to the children list element.
     *
     * e.g. array('class' => 'foobar', 'style' => 'bar: foo')
     *
     * @var array
     */
    protected $childrenAttributes = [];

    /**
     * HTML attributes to add to items link.
     *
     * e.g. array('class' => 'foobar', 'style' => 'bar: foo')
     *
     * @var array
     */
    protected $linkAttributes = [];

    /**
     * HTML attributes to add to the items label.
     *
     * e.g. array('class' => 'foobar', 'style' => 'bar: foo')
     *
     * @var array
     */
    protected $labelAttributes = [];

    /**
     * Hashmap for extra stuff associated to the node.
     *
     * @var array
     */
    protected $extras = [];

    /**
     * Parameters to use when generating the route.
     *
     * Used with the "route" option.
     *
     * @var array
     */
    protected $routeParameters = [];

    /**
     * Set to false to not render.
     *
     * @var bool
     */
    protected $display = true;

    /**
     * Set to false to not render the children.
     *
     * @var bool
     */
    protected $displayChildren = true;

    /**
     * Generate an absolute route.
     *
     * To be used with the "content" or "route" option.
     *
     * @var bool
     */
    protected $routeAbsolute = false;

    public function __construct($name = null)
    {
        $this->name = $name;
        $this->children = new ArrayCollection();
    }

    /**
     * Return ID of this menu node.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets ID of this menu node.
     *
     * @param $id string
     *
     * @return MenuNodeBase - this instance
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the name of this node (used in ID).
     *
     * @param string $name
     *
     * @return MenuNodeBase - this instance
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Return the label assigned to this menu node.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set label for this menu node.
     *
     * @param $label string
     *
     * @return MenuNodeBase - this instance
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Return the URI this menu node points to.
     *
     * @return string URI
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set the URI.
     *
     * @param $uri string
     *
     * @return MenuNodeBase - this instance
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Return the route name.
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set the route name.
     *
     * @param $route string - name of route
     *
     * @return MenuNodeBase - this instance
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Return the attributes associated with this menu node.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set the attributes associated with this menu node.
     *
     * @param $attributes array
     *
     * @return MenuNodeBase - this instance
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Return the given attribute, optionally specifying a default value.
     *
     * @param string $name    The name of the attribute to return
     * @param string $default The value to return if the attribute doesn't exist
     *
     * @return string
     */
    public function getAttribute($name, $default = null)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        return $default;
    }

    /**
     * Set the named attribute.
     *
     * @param string $name  attribute name
     * @param string $value attribute value
     *
     * @return MenuNodeBase - this instance
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Return the children attributes.
     *
     * @return array
     */
    public function getChildrenAttributes()
    {
        return $this->childrenAttributes;
    }

    /**
     * Set the children attributes.
     *
     * @param array $attributes
     *
     * @return MenuNodeBase - this instance
     */
    public function setChildrenAttributes(array $attributes)
    {
        $this->childrenAttributes = $attributes;

        return $this;
    }

    /**
     * Get all child menu nodes of this menu node. This will filter out all
     * non-NodeInterface children.
     *
     * @return NodeInterface[]
     */
    public function getChildren(): \Traversable
    {
        $children = [];
        foreach ($this->children as $child) {
            if (!$child instanceof NodeInterface) {
                continue;
            }
            $children[] = $child;
        }

        return new \ArrayIterator($children);
    }

    /**
     * Add a child menu node under this node.
     *
     * @param NodeInterface $child
     *
     * @return NodeInterface The newly added child node
     */
    public function addChild(NodeInterface $child)
    {
        $this->children[] = $child;

        return $child;
    }

    /**
     * Remove a child menu node.
     *
     * @param NodeInterface $child
     *
     * @return MenuNodeBase $this
     */
    public function removeChild(NodeInterface $child)
    {
        $this->children->removeElement($child);

        return $this;
    }

    /**
     * Gets the route parameters.
     *
     * @return array
     */
    public function getRouteParameters()
    {
        return $this->routeParameters;
    }

    /**
     * Sets the route parameters.
     *
     * @param array $routeParameters
     *
     * @return MenuNodeBase - this instance
     */
    public function setRouteParameters($routeParameters)
    {
        $this->routeParameters = $routeParameters;

        return $this;
    }

    /**
     * Get extra information associated with this node.
     *
     * @return array
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * Set extra information associated with this node.
     *
     * @param array $extras
     *
     * @return MenuNodeBase - this instance
     */
    public function setExtras(array $extras)
    {
        $this->extras = $extras;

        return $this;
    }

    /**
     * Get the link HTML attributes.
     *
     * @return array
     */
    public function getLinkAttributes()
    {
        return $this->linkAttributes;
    }

    /**
     * Set the link HTML attributes as associative array.
     *
     * @param array $linkAttributes
     *
     * @return MenuNodeBase - this instance
     */
    public function setLinkAttributes($linkAttributes)
    {
        $this->linkAttributes = $linkAttributes;

        return $this;
    }

    /**
     * Get the label HTML attributes.
     *
     * @return array
     */
    public function getLabelAttributes()
    {
        return $this->labelAttributes;
    }

    /**
     * Set the label HTML attributes as associative array.
     *
     * @param array $labelAttributes
     *
     * @return MenuNodeBase - this instance
     */
    public function setLabelAttributes($labelAttributes)
    {
        $this->labelAttributes = $labelAttributes;

        return $this;
    }

    /**
     * Whether to display this menu node.
     *
     * @return bool
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * Set whether to display this menu node.
     *
     * @param bool $display
     *
     * @return MenuNodeBase - this instance
     */
    public function setDisplay($display)
    {
        $this->display = $display;

        return $this;
    }

    /**
     * Whether to display the children of this menu node.
     *
     * @return bool
     */
    public function getDisplayChildren()
    {
        return $this->displayChildren;
    }

    /**
     * Set whether to display the children of this menu node.
     *
     * @param bool $displayChildren
     *
     * @return MenuNodeBase - this instance
     */
    public function setDisplayChildren($displayChildren)
    {
        $this->displayChildren = $displayChildren;

        return $this;
    }

    /**
     * Whether to generate absolute links for route or content.
     *
     * @return bool
     */
    public function getRouteAbsolute()
    {
        return $this->routeAbsolute;
    }

    /**
     * Set whether to generate absolute links when generating from a route
     * or the content.
     *
     * @param bool $routeAbsolute
     *
     * @return MenuNodeBase - this instance
     */
    public function setRouteAbsolute($routeAbsolute)
    {
        $this->routeAbsolute = $routeAbsolute;

        return $this;
    }

    /**
     * Whether this menu node can be displayed, meaning it is set to display
     * and it does have a non-empty label.
     *
     * @return bool
     */
    public function isDisplayable()
    {
        return $this->getDisplay() && $this->getLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        return [
            'uri' => $this->getUri(),
            'route' => $this->getRoute(),
            'label' => $this->getLabel(),
            'attributes' => $this->getAttributes(),
            'childrenAttributes' => $this->getChildrenAttributes(),
            'display' => $this->isDisplayable(),
            'displayChildren' => $this->getDisplayChildren(),
            'routeParameters' => $this->getRouteParameters(),
            'routeAbsolute' => $this->getRouteAbsolute(),
            'linkAttributes' => $this->getLinkAttributes(),
            'labelAttributes' => $this->getLabelAttributes(),
        ];
    }
}
