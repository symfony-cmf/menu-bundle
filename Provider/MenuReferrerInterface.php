<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Provider;

interface MenuReferrerInterface
{
    /**
     * Whether or not the class has a menu
     * 
     * @access public
     * @return bool
     */
    public function hasMenu();

    /**
     * Get the name of the menu
     * 
     * @access public
     * @return string
     */
    public function getMenuName();

    /**
     * Get an array of options for the menu to pass to the provider
     * 
     * @access public
     * @return array
     */
    public function getMenuOptions();
}
