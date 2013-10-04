<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;

/**
 * Cmi Tests
 *
 * Tests actions for current menu item functionality.
 */
class VoterController extends Controller
{
    protected function getDm()
    {
        return $this->container->get('doctrine_phpcr.odm.document_manager');
    }

    public function defaultAction(Request $request)
    {
        return $this->render('::tests/voter/default.html.twig');
    }

    public function requestContentIdentityAction(Request $request)
    {
        $content = $request->get(DynamicRouter::CONTENT_KEY);
        if (!$content) {
            $content = $this->getDm()->find(null, '/test/content-1');
            $request->attributes->set(DynamicRouter::CONTENT_KEY, $content);

            return $this->render('::tests/voter/requestContentVoterActive.html.twig', array('content' => $content));
        }

        return $this->render('::tests/voter/requestContent.html.twig', array('content' => $content));
    }

    public function blogAction(Request $request)
    {
        return $this->render('::tests/voter/blog.html.twig');
    }

    public function articlesAction(Request $request)
    {
        return $this->render('::tests/voter/articles.html.twig');
    }

    public function postAction(Request $request)
    {
        $content = $request->get(DynamicRouter::CONTENT_KEY);
        return $this->render('::tests/voter/post.html.twig', array('content' => $content));
    }

    public function urlPrefixAction(Request $request)
    {
        return $this->render('::tests/voter/requestContent.html.twig', array('content' => $content));
    }
}
