<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Provider;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Menu\FactoryInterface;
use Knp\Menu\NodeInterface;
use Knp\Menu\Provider\MenuProviderInterface;

class PhpcrMenuProvider implements MenuProviderInterface
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
     * If this is null, the manager registry will return the default manager.
     *
     * @var string|null Name of object manager to use
     */
    protected $managerName;

    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @param ContainerInterface $container di container to get request from to
     *      know current request uri
     * @param FactoryInterface $factory the menu factory to create the menu
     *      item with the root document (usually ContentAwareFactory)
     * @param ManagerRegistry $managerRegistry manager registry service to use in conjunction
     *      with the manager name to load the load menu root document
     * @param string $menuRoot root id of the menu
     */
    public function __construct(
        ContainerInterface $container,
        FactoryInterface $factory,
        ManagerRegistry $managerRegistry,
        $menuRoot
    ) {
        $this->container = $container;
        $this->factory = $factory;
        $this->managerRegistry = $managerRegistry;
        $this->menuRoot = $menuRoot;
    }

    /**
     * Set the object manager name to use for this loader. If not set, the
     * default manager as decided by the manager registry will be used.
     *
     * @param string|null $managerName
     */
    public function setManagerName($managerName)
    {
        $this->managerName = $managerName;
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
     * @param  string                    $name
     * @param  array                     $options
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException
     */
    public function get($name, array $options = array())
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('The menu name may not be empty');
        }

        $menu = $this->getObjectManager()->find(null, $this->menuRoot . '/' . $name);
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
        $menu = $this->getObjectManager()->find(null, $this->menuRoot . '/' . $name);

        return $menu instanceof NodeInterface;
    }

    /**
     * Get the object manager named $managerName from the registry.
     *
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        return $this->managerRegistry->getManager($this->managerName);
    }
}
