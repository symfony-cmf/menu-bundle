<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Twig;

use Symfony\Cmf\Bundle\MenuBundle\Templating\MenuHelper;

class MenuExtension extends \Twig_Extension
{
    /**
     * @var MenuHelper
     */
    private $helper;

    public function __construct(MenuHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('cmf_menu_get_breadcrumbs_array', array($this->helper, 'getBreadcrumbsArray')),
            new \Twig_SimpleFunction('cmf_menu_get_current_item', array($this->helper, 'getCurrentItem')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cmf_menu';
    }
}
