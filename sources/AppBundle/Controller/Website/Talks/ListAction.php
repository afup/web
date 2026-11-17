<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Talks;

use AppBundle\Antennes\AntenneRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Talk\TalkListQuery;
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
        #[Autowire(env: 'ALGOLIA_APP_ID')]
        private readonly string $algoliaAppId,
        #[Autowire(env: 'ALGOLIA_FRONTEND_API_KEY')]
        private readonly string $algoliaFrontendApikey,
        private readonly AntenneRepository $antennesRepository,
        private readonly SpeakerRepository $speakerRepository,
        private readonly TalkRepository $talkRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $query = new TalkListQuery($request->query->all());
        $speakerLabels = $query->getFacetValues(TalkListQuery::FACET_SPEAKER);
        $eventTitles = $query->getFacetValues(TalkListQuery::FACET_EVENT);

        $now = new \DateTimeImmutable();
        foreach ($speakerLabels as $label) {
            if (!$this->speakerRepository->hasPublishedTalkForLabel($label, $now)) {
                throw $this->createNotFoundException('Aucun talk publié pour ce speaker');
            }
        }
        foreach ($eventTitles as $eventTitle) {
            if (!$this->talkRepository->hasPublishedTalkForEventTitle($eventTitle, $now)) {
                throw $this->createNotFoundException('Aucun talk publié pour cet événement');
            }
        }

        $title = 'Historique des conférences de l\'AFUP';
        $canonical = $this->generateUrl('talks_list', referenceType: UrlGeneratorInterface::ABSOLUTE_URL);
        if (isset($speakerLabels[0])) {
            $label = $speakerLabels[0];
            $title = 'Les vidéos de ' . $label;
            $canonical = $this->generateUrl('talks_list', [
                'fR' => [TalkListQuery::FACET_SPEAKER => [$label]],
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        } elseif (isset($eventTitles[0])) {
            $label = $eventTitles[0];
            $title = $label . ' les vidéos';
            $canonical = $this->generateUrl('talks_list', [
                'fR' => [TalkListQuery::FACET_EVENT => [$label]],
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return $this->view->render('site/talks/list.html.twig', [
            'title' => $title,
            'canonical' => $canonical,
            'noindex' => !$query->isIndexable(),
            'antennes' => $this->antennesRepository->getAllSortedByLabels(),
            'algolia_app_id' => $this->algoliaAppId,
            'algolia_api_key' => $this->algoliaFrontendApikey,
        ]);
    }
}
