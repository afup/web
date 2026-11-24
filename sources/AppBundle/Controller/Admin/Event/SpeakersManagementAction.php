<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Speaker\ScheduledSpeakersProvider;
use AppBundle\SpeakerInfos\SpeakersExpensesStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EVENT_READER')]
class SpeakersManagementAction extends AbstractController
{
    public function __construct(
        private readonly ScheduledSpeakersProvider $scheduledSpeakersProvider,
        private readonly SpeakersExpensesStorage $speakersExpensesStorage,
    ) {}

    public function __invoke(AdminEventSelection $eventSelection): Response
    {
        $event = $eventSelection->event;
        $speakers = [];
        foreach ($this->scheduledSpeakersProvider->getScheduledSpeakers((int) $event->getId(), true) as $row) {
            $files = $this->speakersExpensesStorage->getFiles($row['speaker']);
            $speakers[] = [
                'speaker' => $row['speaker'],
                'hasExpensesFiles' => count($files) >= 1,
            ];
        }

        return $this->render('admin/event/speakers_management.html.twig', [
            'event' => $event,
            'speakers' => $speakers,
            'event_select_form' => $eventSelection->selectForm(),
        ]);
    }
}
