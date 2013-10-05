<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ODM\PHPCR\DocumentManager;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu;
use Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\Document\Content;
use Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\Document\Post;
use Doctrine\ODM\PHPCR\Document\Generic;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route;
use PHPCR\Util\NodeHelper;

class LoadMenuData implements FixtureInterface, DependentFixtureInterface
{
    protected $root;
    protected $menuRoot;
    protected $routeRoot;

    public function getDependencies()
    {
        return array(
            'Symfony\Cmf\Component\Testing\DataFixtures\PHPCR\LoadBaseData',
        );
    }

    public function load(ObjectManager $manager)
    {
        $this->root = $manager->find(null, '/test');

        NodeHelper::createPath($manager->getPhpcrSession(), '/test/menus');
        NodeHelper::createPath($manager->getPhpcrSession(), '/test/routes/contents');
        $this->menuRoot = $manager->find(null, '/test/menus');
        $this->routeRoot = $manager->find(null, '/test/routes');

        $this->loadMainMenu($manager);
        $this->loadVoterMenu($manager);

        $manager->flush();
    }

    protected function loadMainMenu(DocumentManager $manager)
    {
        $content = new Content;
        $content->setTitle('Menu Item Content 1');
        $content->setId('/test/content-menu-item-1');

        $menu = new Menu;
        $menu->setName('test-menu');
        $menu->setLabel('Test Menu');
        $menu->setParent($this->menuRoot);
        $manager->persist($menu);

        $menuNode = new MenuNode;
        $menuNode->setParent($menu);
        $menuNode->setLabel('item-1');
        $menuNode->setName('item-1');
        $manager->persist($menuNode);

        $content->addMenuNode($menuNode);

        $menuNode = new MenuNode;
        $menuNode->setParent($menu);
        $menuNode->setLabel('This node has a URI');
        $menuNode->setUri('http://www.example.com');
        $menuNode->setName('item-2');
        $manager->persist($menuNode);

        $content->addMenuNode($menuNode);

        $subNode = new MenuNode;
        $subNode->setParent($menuNode);
        $subNode->setLabel('@todo this node should have content');
        $subNode->setName('sub-item-1');
        $manager->persist($subNode);

        $subNode = new MenuNode;
        $subNode->setParent($menuNode);
        $subNode->setLabel('This node has an assigned route');
        $subNode->setName('sub-item-2');
        $subNode->setRoute('link_test_route');
        $manager->persist($subNode);

        $subNode = new MenuNode;
        $subNode->setParent($menuNode);
        $subNode->setLabel('This node has an assigned route with parameters');
        $subNode->setName('sub-item-3');
        $subNode->setRoute('link_test_route_with_params');
        $subNode->setRouteParameters(array('foo' => 'bar', 'bar' => 'foo'));
        $manager->persist($subNode);

        $menuNode = new MenuNode;
        $menuNode->setParent($menu);
        $menuNode->setLabel('item-3');
        $menuNode->setName('item-3');
        $manager->persist($menuNode);

        $menu = new Menu;
        $menu->setName('another-menu');
        $menu->setLabel('Another Menu');
        $menu->setParent($this->menuRoot);
        $manager->persist($menu);

        $menuNode = new MenuNode;
        $menuNode->setParent($menu);
        $menuNode->setLabel('This node has uri, route and content set. but linkType is set to route');
        $menuNode->setLinkType('route');
        $menuNode->setUri('http://www.example.com');
        $menuNode->setRoute('link_test_route');
        $menuNode->setName('item-1');
        $manager->persist($menuNode);

        $menuNode = new MenuNode;
        $menuNode->setParent($menu);
        $menuNode->setLabel('item-2');
        $menuNode->setName('item-2');
        $manager->persist($menuNode);

        $manager->persist($content);
    }

    protected function loadVoterMenu(DocumentManager $manager)
    {
        // test content
        $content = new Content;
        $content->setTitle('Content 1');
        $content->setId('/test/content-1');
        $manager->persist($content);

        $route = new Route();
        $route->setId('/test/routes/contents/content-1');
        $route->setDefault('_controller', 'Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\Controller\VoterController::requestContentIdentityAction');
        $route->setContent($content);
        $manager->persist($route);

        // test blog
        $blog = new Content;
        $blog->setTitle('Blog');
        $blog->setId('/test/blog-1');
        $manager->persist($blog);

        $route = new Route();
        $route->setId('/test/routes/blog');
        $route->setDefault('_controller', 'Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\Controller\VoterController::blogAction');
        $route->setContent($blog);
        $manager->persist($route);

        // test blog post
        $post = new Post;
        $post->setTitle('My Post');
        $post->setId('/test/blog-1/my-post');
        $manager->persist($post);

        $route = new Route();
        $route->setId('/test/routes/blog/my-post');
        $route->setDefault('_controller', 'Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\Controller\VoterController::postAction');
        $route->setContent($post);
        $manager->persist($route);

        // test articles
        $articles = new Content;
        $articles->setTitle('Articles Index');
        $articles->setId('/test/articles');
        $manager->persist($articles);

        $articlesRoute = new Route();
        $articlesRoute->setId('/test/routes/articles');
        $articlesRoute->setDefault('_controller', 'Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\Controller\VoterController::articlesAction');
        $articlesRoute->setContent($articles);
        $articlesRoute->setOption('currentUriPrefix', '/articles');
        $manager->persist($articlesRoute);

        $article1 = new Content();
        $article1->setTitle('Article 1');
        $article1->setId('/test/article-1');
        $manager->persist($article1);

        $route = new Route();
        $route->setId('/test/routes/articles/some-category');
        $manager->persist($route);

        $route = new Route();
        $route->setId('/test/routes/articles/some-category/article-1');
        $route->setDefault('_controller', 'Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\Controller\VoterController::postAction');
        $route->setContent($article1);
        $manager->persist($route);

        // menu items
        $menu = new Menu;
        $menu->setName('side-menu');
        $menu->setLabel('Side Menu');
        $menu->setParent($this->menuRoot);
        $manager->persist($menu);

        $menuNode = new MenuNode;
        $menuNode->setParent($menu);
        $menuNode->setLabel('Default Behavior');
        $menuNode->setName('default');
        $menuNode->setRoute('current_menu_item_default');
        $manager->persist($menuNode);

        $menuNode = new MenuNode;
        $menuNode->setParent($menu);
        $menuNode->setLabel('Request Content Identity Voter');
        $menuNode->setName('request-content-identity-voter');
        $menuNode->setContent($content);
        $manager->persist($menuNode);

        $menuNode = new MenuNode;
        $menuNode->setParent($menu);
        $menuNode->setLabel('URI Prefix Voter');
        $menuNode->setName('uri-prefix-voter');
        $menuNode->setContent($articlesRoute);
        $manager->persist($menuNode);

        $menuNode = new MenuNode;
        $menuNode->setParent($menu);
        $menuNode->setLabel('Request Parent Content Identity Voter');
        $menuNode->setName('request-parent-content-identity-voter');
        $menuNode->setContent($blog);
        $manager->persist($menuNode);
    }
}
