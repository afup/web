<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Talks;

use AppBundle\Antennes\Entity\Repository\AntenneRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
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
        private readonly TalkRepository $talkRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $facetRedirect = $this->redirectSpeakerFacet($request) ?? $this->redirectEventFacet($request);
        if (null !== $facetRedirect) {
            return $facetRedirect;
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
        $label = $this->getSingleFacet($request, 'speakers.label');
        if (null === $label) {
            return null;
        }

        $identity = $this->speakerProfileResolver->getIdentityByLabel($label);
        if (null === $identity) {
            return null;
        }

        return $this->redirectToRoute('talks_speaker', ['slug' => $identity->slug], Response::HTTP_MOVED_PERMANENTLY);
    }

    /**
     * L'ancienne URL d'une édition, `/talks/?fR[event.title][0]=…` sans aucun autre paramètre,
     * redirige vers sa page si elle a des talks publiés. Une facette combinée à d'autres filtres reste une recherche.
     */
    private function redirectEventFacet(Request $request): ?RedirectResponse
    {
        $title = $this->getSingleFacet($request, 'event.title');
        if (null === $title) {
            return null;
        }

        foreach ($this->talkRepository->getEventTalksSummaries() as $summary) {
            if ($summary->event->getTitle() === $title) {
                return $this->redirectToRoute('talks_event', ['path' => $summary->event->getPath()], Response::HTTP_MOVED_PERMANENTLY);
            }
        }

        return null;
    }

    /**
     * La valeur de la facette quand la requête n'a aucun autre paramètre que `fR[$facet][0]`.
     */
    private function getSingleFacet(Request $request, string $facet): ?string
    {
        $values = $request->query->all('fR')[$facet] ?? null;
        $value = is_array($values) ? $values[0] ?? null : null;

        if (!is_string($value) || $request->query->all() !== ['fR' => [$facet => [$value]]]) {
            return null;
        }

        return $value;
    }
}
