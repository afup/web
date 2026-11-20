<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\Event\Entity\Repository\PersonneConferencierRepository;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Speaker\SpeakerSearchResult;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Fiche personne : ses lignes conférencier (une par édition), les suggestions de rattachement et la fusion.
 */
#[IsGranted('ROLE_EVENT_WRITER')]
final class PersonneConferencierAction extends AbstractController
{
    public function __construct(
        private readonly PersonneConferencierRepository $personneConferencierRepository,
        private readonly SpeakerRepository $speakerRepository,
        private readonly EventRepository $eventRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $personne = $this->personneConferencierRepository->find($request->query->getInt('id'));
        if ($personne === null) {
            throw $this->createNotFoundException('Personne non trouvée');
        }
        $search = $request->query->getString('q');

        $eventTitles = [];
        foreach ($this->eventRepository->getAll() as $event) {
            if ($event->getId() !== null) {
                $eventTitles[$event->getId()] = $event->getTitle();
            }
        }

        return $this->render('admin/speaker/personne.html.twig', [
            'personne' => $personne,
            'speakers' => $this->loadSpeakers($this->personneConferencierRepository->findSpeakerIds($personne->id)),
            'suggestions' => $this->loadSpeakers($this->personneConferencierRepository->findSuggestedSpeakerIds($personne->id)),
            'search' => $search,
            'mergeCandidates' => array_values(array_filter(
                $this->personneConferencierRepository->searchAcrossEvents($search),
                static fn(SpeakerSearchResult $result): bool => $result->personneId !== null && $result->personneId !== $personne->id,
            )),
            'eventTitles' => $eventTitles,
        ]);
    }

    /**
     * @param list<int> $speakerIds
     * @return list<Speaker>
     */
    private function loadSpeakers(array $speakerIds): array
    {
        $speakers = [];
        foreach ($speakerIds as $speakerId) {
            $speaker = $this->speakerRepository->get($speakerId);
            if ($speaker instanceof Speaker) {
                $speakers[] = $speaker;
            }
        }

        return $speakers;
    }
}
