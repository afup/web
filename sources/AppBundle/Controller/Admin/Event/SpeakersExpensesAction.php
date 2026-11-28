<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Speaker\ScheduledSpeakersProvider;
use AppBundle\SpeakerInfos\SpeakersExpensesStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EVENT_READER')]
class SpeakersExpensesAction extends AbstractController
{
    public function __construct(
        private readonly ScheduledSpeakersProvider $scheduledSpeakersProvider,
        private readonly SpeakersExpensesStorage $speakersExpensesStorage,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
        $id = $request->query->get('id');

        $event = $eventSelection->event;

        $speakers = [];
        foreach ($this->scheduledSpeakersProvider->getScheduledSpeakers((int) $event->getId(), true) as $row) {
            $files = $this->speakersExpensesStorage->getFiles($row['speaker']);
            $speakers[] = [
                'speaker' => $row['speaker'],
                'hasExpensesFiles' => $files,
            ];
        }

        return $this->render('admin/event/speakers_expenses.html.twig', [
            'event' => $event,
            'speakers' => $speakers,
            'event_select_form' => $eventSelection->selectForm(),
        ]);
    }
}
