<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Model;

use Knp\Menu\NodeInterface;

/**
 * Provide access to read and write the menu options.
 *
 * @author Mojtaba Koosej <mkoosej@gmail.com>
 */

interface MenuOptionsInterface extends NodeInterface
{
    /**
     * Whether or not to display this menu
     *
     * @return boolean
     */
    function getDisplay();

    /**
     * Set whether or not this menu should be displayed
     *
     * @param boolean $bool
     *
     * @return MenuOptionsInterface
     */
    function setDisplay($bool);

    /**
     * Whether or not this menu should show its children.
     *
     * @return boolean
     */
    function getDisplayChildren();

    /**
     * Set whether or not this menu should show its children
     *
     * @param boolean $bool
     *
     * @return MenuOptionsInterface
     */
    function setDisplayChildren($bool);

     /**
     * Return the attributes associated with this menu node
     *
     * @return array
     */
    function getAttributes();

     /**
     * Set the attributes associated with this menu node
     *
     * @param $attributes array
     *
     * @return Page The current Page instance
     */
    function setAttributes(array $attributes);

    /**
     * Return the given attribute, optionally specifying a default value
     *
     * @param string $name    The name of the attribute to return
     * @param string $default The value to return if the attribute doesn't exist
     *
     * @return string
     */
    function getAttribute($name, $default = null);

    /**
     * Set the named attribute
     *
     * @param string $name  attribute name
     * @param string $value attribute value
     *
     * @return Page The current Page instance
     */
    function setAttribute($name, $value);

    /**
     * Get the link HTML attributes.
     *
     * @return array
     */
    function getLinkAttributes();

    /**
     * Set the link HTML attributes as associative array.
     *
     * @param array $linkAttributes
     *
     * @return Page The current Page instance
     */
    function setLinkAttributes($linkAttributes);

    /**
     * Return the children attributes
     *
     * @return array
     */
    function getChildrenAttributes();

    /**
     * Set the children attributes
     *
     * @param array $attributes
     *
     * @return Page The current Page instance
     */
    function setChildrenAttributes(array $childrenAttributes);

    /**
     * Get the label HTML attributes.
     *
     * @return array
     */
    function getLabelAttributes();

    /**
     * Set the label HTML attributes as associative array.
     *
     * @param array $labelAttributes
     *
     * @return Page The current Page instance
     */
    function setLabelAttributes($labelAttributes);

}
