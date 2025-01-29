<?php

namespace AppBundle\Controller\Website;

use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MeetupsController extends Controller
{
    private ViewRenderer $view;

    public function __construct(ViewRenderer $view)
    {
        $this->view = $view;
    }

    public function listAction(Request $request)
    {
        return $this->view->render('site/meetups/list.html.twig',[
            'algolia_app_id' => $this->getParameter('algolia_app_id'),
            'algolia_api_key' => $this->getParameter('algolia_frontend_api_key'),
            'source' => $request->get('src'),
        ]);
    }
}
