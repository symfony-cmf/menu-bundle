<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('::index.html.twig');
    }

    public function renderAction(Request $request)
    {
        return $this->render('::tests/render.html.twig');
    }

    public function linkTestRouteAction(Request $request)
    {
        return $this->render('::linkTestRoute.html.twig');
    }
}
