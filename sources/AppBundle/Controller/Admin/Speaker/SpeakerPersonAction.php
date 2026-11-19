<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\Event\Entity\Repository\SpeakerPersonRepository;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Speaker\SpeakerSearchResult;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Fiche personne : ses lignes conférencier (une par édition), les suggestions de rattachement et la fusion.
 */
#[IsGranted('ROLE_EVENT_WRITER')]
final class SpeakerPersonAction extends AbstractController
{
    public function __construct(
        private readonly SpeakerPersonRepository $speakerPersonRepository,
        private readonly SpeakerRepository $speakerRepository,
        private readonly EventRepository $eventRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $person = $this->speakerPersonRepository->find($request->query->getInt('id'));
        if ($person === null) {
            throw $this->createNotFoundException('Personne non trouvée');
        }
        $search = $request->query->getString('q');

        $eventTitles = [];
        foreach ($this->eventRepository->getAll() as $event) {
            if ($event->getId() !== null) {
                $eventTitles[$event->getId()] = $event->getTitle();
            }
        }

        return $this->render('admin/speaker/person.html.twig', [
            'person' => $person,
            'speakers' => $this->speakerRepository->findByPerson($person->id),
            'suggestions' => $this->speakerRepository->findLinkSuggestions($person->id),
            'search' => $search,
            'mergeCandidates' => array_values(array_filter(
                $this->speakerRepository->searchAcrossEvents($search),
                static fn(SpeakerSearchResult $result): bool => $result->personId !== null && $result->personId !== $person->id,
            )),
            'eventTitles' => $eventTitles,
        ]);
    }
}
