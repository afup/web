<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MeetupsController extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly string $algoliaAppId,
        private readonly string $algoliaFrontendApikey,
    ) {
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
