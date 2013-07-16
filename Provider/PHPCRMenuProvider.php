<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Provider;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Knp\Menu\FactoryInterface;
use Knp\Menu\NodeInterface;
use Knp\Menu\Provider\MenuProviderInterface;

class PHPCRMenuProvider implements MenuProviderInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var FactoryInterface
     */
    protected $factory = null;

    /**
     * @var \Doctrine\ODM\PHPCR\ModelManager
     */
    protected $dm;

    /**
     * base for menu ids
     * @var string
     */
    protected $menuRoot;

    /**
     * doctrine document class name
     * @var string
     */
    protected $className;

    /**
     * @param ContainerInterface $container di container to get request from to
     *      know current request uri
     * @param FactoryInterface $factory the menu factory to create the menu
     *      item with the root document (usually ContentAwareFactory)
     * @param string $objectManagerName document manager service name to load menu root
     *      document from
     * @param string $menuRoot root id of the menu
     * @param string $className the menu document class name. with phpcr-odm
     *      this can be null
     */
    public function __construct(ContainerInterface $container, FactoryInterface $factory, $objectManagerName, $menuRoot)
    {
        $this->container = $container;
        $this->factory = $factory;
        $this->dm = $this->container->get('doctrine_phpcr')->getManager($objectManagerName);
        $this->menuRoot = $menuRoot;
    }

    /**
     * @param string $menuRoot
     */
    public function setMenuRoot($menuRoot)
    {
        $this->menuRoot = $menuRoot;
    }

    /**
     * @return string
     */
    public function getMenuRoot()
    {
        return $this->menuRoot;
    }

    /**
     * Get a menu node by name
     *
     * @param  string $name
     * @param  array  $options
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException
     */
    public function get($name, array $options = array())
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('The menu name may not be empty');
        }

        $menu = $this->dm->find(null, $this->menuRoot . '/' . $name);
        if ($menu === null) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        if (! $menu instanceof NodeInterface) {
            throw new \InvalidArgumentException("Menu at '$name' is not a valid menu node");
        }

        $menuNode = $this->factory->createFromNode($menu);
        if (empty($menuNode)) {
            throw new \InvalidArgumentException("Menu at '$name' is misconfigured (f.e. the route might be incorrect) and could therefore not be instanciated");
        }

        $menuNode->setCurrentUri($this->container->get('request')->getRequestUri());

        return $menuNode;
    }

    /**
     * Check if a menu node exists
     *
     * @param  string $name
     * @param  array  $options
     * @return bool
     */
    public function has($name, array $options = array())
    {
        $menu = $this->dm->find(null, $this->menuRoot . '/' . $name);
        return $menu instanceof NodeInterface;
    }
}
