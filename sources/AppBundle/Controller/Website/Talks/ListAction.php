<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Talks;

use AppBundle\Antennes\AntenneRepository;
use AppBundle\Event\Speaker\SpeakerProfileResolver;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ListAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        #[Autowire(env: 'ALGOLIA_APP_ID')]
        private readonly string $algoliaAppId,
        #[Autowire(env: 'ALGOLIA_FRONTEND_API_KEY')]
        private readonly string $algoliaFrontendApikey,
        private readonly AntenneRepository $antennesRepository,
        private readonly SpeakerProfileResolver $speakerProfileResolver,
    ) {}

    public function __invoke(Request $request): Response
    {
        $speakerRedirect = $this->redirectSpeakerFacet($request);
        if (null !== $speakerRedirect) {
            return $speakerRedirect;
        }

        $title = 'Historique des conférences de l\'AFUP';
        $canonical = $this->generateUrl('talks_list', referenceType: UrlGeneratorInterface::ABSOLUTE_URL);
        if (isset($request->query->all('fR')['speakers.label'][0])) {
            $label = $request->query->all('fR')['speakers.label'][0];
            $title = 'Les vidéos de ' . $label;
            $canonical = $this->generateUrl('talks_list', [
                'fR' => ['speakers.label' => [$label]],
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        } elseif (isset($request->query->all('fR')['event.title'][0])) {
            $label = $request->query->all('fR')['event.title'][0];
            $title = $request->query->all('fR')['event.title'][0] . ' les vidéos';
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

    /**
     * L'ancienne URL d'un speaker, `/talks/?fR[speakers.label][0]=…` sans aucun autre paramètre,
     * redirige vers sa page. Une facette combinée à d'autres filtres reste une recherche.
     */
    private function redirectSpeakerFacet(Request $request): ?RedirectResponse
    {
        $labels = $request->query->all('fR')['speakers.label'] ?? null;
        $label = is_array($labels) ? $labels[0] ?? null : null;

        if (!is_string($label) || $request->query->all() !== ['fR' => ['speakers.label' => [$label]]]) {
            return null;
        }

        $identity = $this->speakerProfileResolver->getIdentityByLabel($label);
        if (null === $identity) {
            return null;
        }

        return $this->redirectToRoute('talks_speaker', ['slug' => $identity->slug], Response::HTTP_MOVED_PERMANENTLY);
    }
}
