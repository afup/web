<?php

namespace AppBundle\Controller;

class TalksController extends SiteBaseController
{
    public function listAction()
    {
        return $this->render(
            ':site:talks/list.html.twig',
            [
                'algolia_app_id' => $this->getParameter('algolia_app_id'),
                'algolia_api_key' => $this->getParameter('algolia_frontend_api_key'),
            ]
        );
    }
}
