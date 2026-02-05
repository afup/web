<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Talks;

use AppBundle\Antennes\AntenneRepository;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ListAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        #[Autowire('%env(ALGOLIA_APP_ID)%')]
        private readonly string $algoliaAppId,
        #[Autowire('%env(ALGOLIA_FRONTEND_API_KEY)%')]
        private readonly string $algoliaFrontendApikey,
        private readonly AntenneRepository $antennesRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $title = 'Historique des conférences de l\'AFUP';
        $canonical = $this->generateUrl('talks_list', referenceType: UrlGeneratorInterface::ABSOLUTE_URL);
        if (isset($request->get('fR')['speakers.label'][0])) {
            $label = $request->get('fR')['speakers.label'][0];
            $title = 'Les vidéos de ' . $label;
            $canonical = $this->generateUrl('talks_list', [
                'fR' => ['speakers.label' => [$label]],
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        } elseif (isset($request->get('fR')['event.title'][0])) {
            $label = $request->get('fR')['event.title'][0];
            $title = $request->get('fR')['event.title'][0] . ' les vidéos';
            $canonical = $this->generateUrl('talks_list', [
                'fR' => ['event.title' => [$label]],
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return $this->view->render('site/talks/list.html.twig', [
            'title' => $title,
            'canonical' => $canonical,
            'antennes' => $this->antennesRepository->getAllSortedByLabels(),
            'algolia_app_id' => $this->algoliaAppId,
            'algolia_api_key' => $this->algoliaFrontendApikey,
        ]);
    }
}
