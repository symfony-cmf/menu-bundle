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
use Jackalope\Session as JackalopeSession;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\NodeInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use PHPCR\SessionInterface;

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
     * {@inheritDoc}
     */
    public function get($name, array $options = array())
    {
        $document = $this->find($name, $options, true);

        if (null === $document) {
            throw new \InvalidArgumentException(sprintf(
                'Menu "%s" could not be located by the PhpcrMenuProvider',
                $name
            ));
        }

        $menuItem = $this->loader->load($document);

        if (empty($menuItem)) {
            throw new \InvalidArgumentException(sprintf(
                'Menu "%s" is misconfigured (f.e. the route might be incorrect) and could therefore not be instansiated',
                $name
            ));
        }

        return $menuItem;
    }

    /**
     * {@inheritDoc}
     */
    public function has($name, array $options = array())
    {
        $document = $this->find($name, $options);

        if (null === $document) {
            return false;
        }

        return true;
    }

    /**
     * Find the named menu or `null` if the menu cannot be located.
     *
     * @param string $name Name of the menu to load
     * @param array  $options
     *
     * @return ItemInterface|null
     *
     * @throws \RuntimeException If the found node does not implement the
     *     correct interface.
     */
    protected function find($name, array $options)
    {
        $manager = $this->getObjectManager();
        $session = $manager->getPhpcrSession();
        $path = PathHelper::absolutizePath($name, $this->getMenuRoot());

        if ($this->getPrefetch() > 0) {
            if ($session instanceof JackalopeSession) {
                $this->jackalopePrefetch($session, $path);
            }

            $this->genericPrefetch($session, $path);
        }

        $menu = $manager->find(null, $path);

        if (null === $menu) {
            return null;
        }

        if (!$menu instanceof NodeInterface) {
            throw new \RuntimeException(sprintf(
                'Menu document at "%s" does not implement Knp\Menu\NodeInterface',
                $path
            ));
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

    /**
     * Special case for Jackalope prefetching.
     */
    private function jackalopePrefetch(JackalopeSession $session, $path)
    {
        $fetchDepth = $session->getSessionOption(JackalopeSession::OPTION_FETCH_DEPTH);

        // under what circumstance would the path not contain the menu root?
        $containsRoot = 0 === strncmp($path, $this->getMenuRoot(), strlen($this->getMenuRoot()));

        if (false === $containsRoot || 0 === $fetchDepth) {
            return $this->genericPrefetch($session, $path);
        }

        try {
            // we have jackalope with a fetch depth. prefetch all menu
            // nodes of all menues.
            $session->getNode($this->getMenuRoot(), $this->getPrefetch() + 1);
        } catch (PathNotFoundException $e) {
            throw new \InvalidArgumentException(sprintf(
                'The menu root "%s" does not exist when prefetching for Jackalope', 
                $this->getMenuRoot()
            ), null, $e);
        }
    }

    /**
     * Generic prefetch
     */
    private function genericPrefetch(SessionInterface $session, $path)
    {
        try {
            $session->getNode($path, $this->getPrefetch());
        } catch (PathNotFoundException $e) {
            throw new \InvalidArgumentException(sprintf(
                'The menu node "%s" does not exist.',
                $path
            ), null, $e);
        }
    }
}
