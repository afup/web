<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\SpeakerInfos\SpeakersExpensesStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SpeakersManagementAction extends AbstractController
{
    public function __construct(
        private readonly SpeakerRepository $speakerRepository,
        private readonly SpeakersExpensesStorage $speakersExpensesStorage,
    ) {}

    public function __invoke(AdminEventSelection $eventSelection): Response
    {
        $event = $eventSelection->event;
        $speakers = $this->speakerRepository->getScheduledSpeakersByEvent($event, true);

        if ($speakers->count() > 0) {
            $speakers = iterator_to_array($speakers->getIterator());
            foreach ($speakers as $k => $speaker) {
                $files = $this->speakersExpensesStorage->getFiles($speaker['speaker']);
                $speakers[$k]['hasExpensesFiles'] = count($files) >= 1;
            }
        }

        return $this->render('admin/event/speakers_management.html.twig', [
            'event' => $event,
            'speakers' => $speakers,
            'event_select_form' => $eventSelection->selectForm(),
        ]);
    }
}
