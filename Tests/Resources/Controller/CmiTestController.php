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
class CmiTestController extends Controller
{
    public function defaultAction(Request $request)
    {
        return $this->render('::tests/cmi/default.html.twig');
    }

    public function requestContentIdentityAction(Request $request)
    {
        $content = $request->get(DynamicRouter::CONTENT_KEY);
        if (!$content) {
            $content = $this->container->get('doctrine_phpcr.odm.document_manager')->find(null, '/test/content-1');
            $request->attributes->set(DynamicRouter::CONTENT_KEY, $content);

            return $this->render('::tests/cmi/requestContentVoterActive.html.twig', array('content' => $content));
        }
        return $this->render('::tests/cmi/requestContent.html.twig', array('content' => $content));
    }
}
