<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * Whether or not to display this menu.
     *
     * @return bool
     */
    public function getDisplay();

    /**
     * Set whether or not this menu should be displayed.
     *
     * @param bool $bool
     *
     * @return MenuOptionsInterface
     */
    public function setDisplay($bool);

    /**
     * Whether or not this menu should show its children.
     *
     * @return bool
     */
    public function getDisplayChildren();

    /**
     * Set whether or not this menu should show its children.
     *
     * @param bool $bool
     *
     * @return MenuOptionsInterface
     */
    public function setDisplayChildren($bool);

    /**
     * Return the attributes associated with this menu node.
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Set the attributes associated with this menu node.
     *
     * @param $attributes array
     *
     * @return Page The current Page instance
     */
    public function setAttributes(array $attributes);

    /**
     * Return the given attribute, optionally specifying a default value.
     *
     * @param string $name    The name of the attribute to return
     * @param string $default The value to return if the attribute doesn't exist
     *
     * @return string
     */
    public function getAttribute($name, $default = null);

    /**
     * Set the named attribute.
     *
     * @param string $name  attribute name
     * @param string $value attribute value
     *
     * @return Page The current Page instance
     */
    public function setAttribute($name, $value);

    /**
     * Get the link HTML attributes.
     *
     * @return array
     */
    public function getLinkAttributes();

    /**
     * Set the link HTML attributes as associative array.
     *
     * @param array $linkAttributes
     *
     * @return Page The current Page instance
     */
    public function setLinkAttributes($linkAttributes);

    /**
     * Return the children attributes.
     *
     * @return array
     */
    public function getChildrenAttributes();

    /**
     * Set the children attributes.
     *
     * @param array $attributes
     *
     * @return Page The current Page instance
     */
    public function setChildrenAttributes(array $childrenAttributes);

    /**
     * Get the label HTML attributes.
     *
     * @return array
     */
    public function getLabelAttributes();

    /**
     * Set the label HTML attributes as associative array.
     *
     * @param array $labelAttributes
     *
     * @return Page The current Page instance
     */
    public function setLabelAttributes($labelAttributes);
}
