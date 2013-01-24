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
     * @var \Doctrine\Common\Persistence\ObjectManager
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
        $menuNode->setCurrentUri($this->container->get('request')->getRequestUri());
        return $menuNode;
    }

    public function has($name, array $options = array())
    {
        $menu = $this->dm->find(null, $this->menuRoot . '/' . $name);
        return $menu !== null;
    }
}
