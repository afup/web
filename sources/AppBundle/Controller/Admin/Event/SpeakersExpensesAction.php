<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\SpeakerInfos\SpeakersExpensesStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpeakersExpensesAction extends AbstractController
{
    public function __construct(
        private readonly SpeakerRepository $speakerRepository,
        private readonly SpeakersExpensesStorage $speakersExpensesStorage,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
        $id = $request->query->get('id');

        $event = $eventSelection->event;

        $speakers = $this->speakerRepository->getScheduledSpeakersByEvent($event, true);
        if ($speakers->count() > 0) {
            $speakers = iterator_to_array($speakers->getIterator());
            foreach ($speakers as $k => $speaker) {
                $files = $this->speakersExpensesStorage->getFiles($speaker['speaker']);
                $speakers[$k]['hasExpensesFiles'] = $files;
            }
        }

        return $this->render('admin/event/speakers_expenses.html.twig', [
            'event' => $event,
            'speakers' => $speakers,
            'event_select_form' => $eventSelection->selectForm(),
        ]);
    }
}
