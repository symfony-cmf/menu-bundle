<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Provider;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ODM\PHPCR\DocumentManager;
use Knp\Menu\Loader\NodeLoader;
use PHPCR\RepositoryException;
use Symfony\Component\HttpFoundation\Request;
use PHPCR\PathNotFoundException;
use PHPCR\Util\PathHelper;
use Jackalope\Session;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\NodeInterface;
use Knp\Menu\Provider\MenuProviderInterface;

class PhpcrMenuProvider implements MenuProviderInterface
{
    /**
     * @var NodeLoader
     */
    protected $loader;

    /**
     * @var Request
     */
    protected $request;

    /**
     * base for menu ids.
     *
     * @var string
     */
    protected $menuRoot;

    /**
     * Depth to use to prefetch all menu nodes. Only used if > 0, otherwise
     * no prefetch is attempted.
     *
     * @var int
     */
    protected $prefetch = 10;

    /**
     * doctrine document class name.
     *
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
     * @param FactoryInterface $factory         the menu factory to create the menu
     *                                          item with the root document (usually ContentAwareFactory)
     * @param ManagerRegistry  $managerRegistry manager registry service to use in conjunction
     *                                          with the manager name to load the load menu root document
     * @param string           $menuRoot        root id of the menu
     */
    public function __construct(
        NodeLoader $loader,
        ManagerRegistry $managerRegistry,
        $menuRoot
    ) {
        $this->loader = $loader;
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
     * Define the depth of menu to prefetch when a menu is accessed.
     *
     * Note that if this PHPCR implementation is jackalope and there is a
     * global fetch depth, the menu provider will prefetch *all* menus at the
     * menu root when a menu is accessed. If it would not do that, loading the
     * parent for one menu root would fetch all menu roots and only one menu
     * would be completely prefetched.
     *
     * @param int $depth
     */
    public function setPrefetch($depth)
    {
        $this->prefetch = intval($depth);
    }

    /**
     * Get the depth to use. A depth <= 0 means no prefetching should be done.
     *
     * @return int The depth to use when fetching menus.
     */
    public function getPrefetch()
    {
        return $this->prefetch;
    }

    /**
     * Set the request.
     *
     * @param Request $request
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * Create the menu subtree starting from name.
     *
     * If the name is not already absolute, it is interpreted relative to the
     * menu root. You can thus pass a name or any relative path with slashes to
     * only load a submenu rather than a whole menu.
     *
     * @param string $name    Name of the menu to load. This can be an
     *                        absolute PHPCR path or one relative to the menu root.
     * @param array  $options
     *
     * @return ItemInterface The menu (sub)tree starting with name.
     *
     * @throws \InvalidArgumentException if the menu can not be found.
     */
    public function get($name, array $options = array())
    {
        $menu = $this->find($name, $options, true);

        $menuItem = $this->loader->load($menu);
        if (empty($menuItem)) {
            throw new \InvalidArgumentException("Menu at '$name' is misconfigured (f.e. the route might be incorrect) and could therefore not be instanciated");
        }

        return $menuItem;
    }

    /**
     * Check if a menu node exists.
     *
     * If this method returns true, it means that you can call get() without
     * an exception.
     *
     * @param string $name    Name of the menu to load. This can be an
     *                        absolute PHPCR path or one relative to the menu root.
     * @param array  $options
     *
     * @return bool Whether a menu with this name can be loaded by this provider.
     */
    public function has($name, array $options = array())
    {
        return $this->find($name, $options, false) instanceof NodeInterface;
    }

    /**
     * @param string $name    Name of the menu to load
     * @param array  $options
     * @param bool   $throw   Whether to throw an exception if the menu is not
     *                        found or no valid menu. Returns false if $throw is false and there
     *                        is no menu at $name.
     *
     * @return object|bool The menu root found with $name or false if $throw
     *                     is false and the menu was not found.
     *
     * @throws \InvalidArgumentException Only if $throw is true throws this
     *                                   exception if the name is empty or no menu found.
     */
    protected function find($name, array $options, $throw)
    {
        if (empty($name)) {
            if ($throw) {
                throw new \InvalidArgumentException('The menu name may not be empty');
            }

            return false;
        }

        $dm = $this->getObjectManager();
        $session = $dm->getPhpcrSession();

        try {
            $path = PathHelper::absolutizePath($name, $this->getMenuRoot());
            PathHelper::assertValidAbsolutePath($path, false, true, $session->getNamespacePrefixes());
        } catch (RepositoryException $e) {
            if ($throw) {
                throw $e;
            }

            return false;
        }

        if ($this->getPrefetch() > 0) {
            try {
                if (
                    $session instanceof Session
                    && 0 < $session->getSessionOption(Session::OPTION_FETCH_DEPTH)
                    && 0 === strncmp($path, $this->getMenuRoot(), strlen($this->getMenuRoot()))
                ) {
                    // we have jackalope with a fetch depth. prefetch all menu
                    // nodes of all menues.
                    $session->getNode($this->getMenuRoot(), $this->getPrefetch() + 1);
                } else {
                    $session->getNode($path, $this->getPrefetch());
                }
            } catch (PathNotFoundException $e) {
                if ($throw) {
                    throw new \InvalidArgumentException(sprintf('The menu root "%s" does not exist.', $this->getMenuRoot()));
                }

                return false;
            }
        }

        $menu = $dm->find(null, $path);
        if (null === $menu) {
            if ($throw) {
                throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
            }

            return false;
        }
        if (!$menu instanceof NodeInterface) {
            if ($throw) {
                throw new \InvalidArgumentException("Menu at '$name' is not a valid menu node");
            }

            return false;
        }

        return $menu;
    }

    /**
     * Get the object manager named $managerName from the registry.
     *
     * @return DocumentManager
     */
    protected function getObjectManager()
    {
        return $this->managerRegistry->getManager($this->managerName);
    }
}
