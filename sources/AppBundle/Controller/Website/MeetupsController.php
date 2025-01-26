<?php

namespace AppBundle\Controller\Website;

use Symfony\Component\HttpFoundation\Request;

class MeetupsController extends SiteBaseController
{
    public function listAction(Request $request)
    {
        return $this->render(
            ':site:meetups/list.html.twig',
            [
                'algolia_app_id' => $this->getParameter('algolia_app_id'),
                'algolia_api_key' => $this->getParameter('algolia_frontend_api_key'),
                'source' => $request->get('src'),
            ]
        );
    }
}
