<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Meetups;

use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ListAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        #[Autowire('%algolia_app_id%')]
        private readonly string $algoliaAppId,
        #[Autowire('%algolia_frontend_api_key%')]
        private readonly string $algoliaFrontendApikey,
    ) {}

    public function __invoke(Request $request): Response
    {
        return $this->view->render('site/meetups/list.html.twig',[
            'algolia_app_id' => $this->algoliaAppId,
            'algolia_api_key' => $this->algoliaFrontendApikey,
            'source' => $request->get('src'),
        ]);
    }
}
