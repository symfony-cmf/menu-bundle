<?php

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

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('cmf_menu_get_breadcrumbs_array', array($this->helper, 'getBreadcrumbsArray')),
            new \Twig_SimpleFunction('cmf_menu_get_current_item', array($this->helper, 'getCurrentItem')),
        );
    }

    public function getName()
    {
        return 'cmf_menu';
    }
}
