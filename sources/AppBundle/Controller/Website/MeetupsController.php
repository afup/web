<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MeetupsController extends AbstractController
{
    private ViewRenderer $view;
    private string $algoliaAppId;
    private string $algoliaFrontendApikey;

    public function __construct(ViewRenderer $view,
                                string $algoliaAppId,
                                string $algoliaFrontendApikey)
    {
        $this->view = $view;
        $this->algoliaAppId = $algoliaAppId;
        $this->algoliaFrontendApikey = $algoliaFrontendApikey;
    }

    public function list(Request $request): Response
    {
        return $this->view->render('site/meetups/list.html.twig',[
            'algolia_app_id' => $this->algoliaAppId,
            'algolia_api_key' => $this->algoliaFrontendApikey,
            'source' => $request->get('src'),
        ]);
    }
}
