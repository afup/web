<?php

namespace AppBundle\Controller\Website;

use AppBundle\WebsiteBlocks;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MeetupsController extends Controller
{
    private WebsiteBlocks $websiteBlocks;

    public function __construct(WebsiteBlocks $websiteBlocks)
    {
        $this->websiteBlocks = $websiteBlocks;
    }

    public function listAction(Request $request)
    {
        return $this->websiteBlocks->render('site/meetups/list.html.twig',[
            'algolia_app_id' => $this->getParameter('algolia_app_id'),
            'algolia_api_key' => $this->getParameter('algolia_frontend_api_key'),
            'source' => $request->get('src'),
        ]);
    }
}
