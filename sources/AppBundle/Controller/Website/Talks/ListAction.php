<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Talks;

use AppBundle\Antennes\Entity\Repository\AntenneRepository;
use AppBundle\Event\Talk\PublishedTalkRepository;
use AppBundle\Event\Talk\EventTalksSummary;
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
    public const int LATEST_TALKS_COUNT = 12;

    /**
     * Paramètres de l'état de recherche qu'instantsearch (urlSync) écrit dans l'URL : requête et filtres.
     */
    private const array SEARCH_PARAMETERS = ['q', 'fR', 'dFR', 'hFR', 'nR', 'tR', 'fE'];

    public function __construct(
        private readonly ViewRenderer $view,
        #[Autowire(env: 'ALGOLIA_APP_ID')]
        private readonly string $algoliaAppId,
        #[Autowire(env: 'ALGOLIA_FRONTEND_API_KEY')]
        private readonly string $algoliaFrontendApikey,
        private readonly AntenneRepository $antennesRepository,
        private readonly SpeakerProfileResolver $speakerProfileResolver,
        private readonly PublishedTalkRepository $publishedTalkRepository,
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

        // Le hub (dernières vidéos, éditions, speakers) ne s'affiche que hors recherche : sur une URL de recherche,
        // il repousserait les résultats sous la ligne de flottaison, et ces URLs ne sont pas destinées à être indexées.
        $showHub = !$this->isSearch($request);
        $latestTalks = $showHub ? $this->publishedTalkRepository->getLatestTalksWithVideo(self::LATEST_TALKS_COUNT) : [];

        // Slug de la page de chaque speaker, par ID de ligne speaker, pour lier les speakers des cartes.
        $speakerSlugs = [];
        foreach ($latestTalks as $latest) {
            foreach ($latest['speakers'] as $speaker) {
                $identity = $this->speakerProfileResolver->getIdentityByPersonneId($speaker->personneId);
                if (null !== $identity) {
                    $speakerSlugs[$speaker->id] = $identity->slug;
                }
            }
        }

        return $this->view->render('site/talks/list.html.twig', [
            'title' => $title,
            'show_hub' => $showHub,
            'latest_talks' => $latestTalks,
            'speaker_slugs' => $speakerSlugs,
            'summaries_by_year' => $showHub ? EventTalksSummary::groupByYear($this->publishedTalkRepository->getEventSummaries()) : [],
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

        foreach ($this->publishedTalkRepository->getEventSummaries() as $summary) {
            if ($summary->event->title === $title) {
                return $this->redirectToRoute('talks_event', ['path' => $summary->event->path], Response::HTTP_MOVED_PERMANENTLY);
            }
        }

        return null;
    }

    /**
     * L'URL porte un état de recherche : une requête, un filtre ou une page de résultats autre que la première.
     */
    private function isSearch(Request $request): bool
    {
        $query = $request->query->all();
        foreach (self::SEARCH_PARAMETERS as $parameter) {
            if (!in_array($query[$parameter] ?? null, [null, '', []], true)) {
                return true;
            }
        }

        $page = $query['p'] ?? 0;

        return is_numeric($page) && (int) $page > 0;
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
