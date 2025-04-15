<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\SpeakerInfos\SpeakersExpensesStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpeakersManagementAction extends AbstractController
{
    private EventActionHelper $eventActionHelper;
    private SpeakerRepository $speakerRepository;
    private SpeakersExpensesStorage $speakersExpensesStorage;

    public function __construct(
        EventActionHelper $eventActionHelper,
        SpeakerRepository $speakerRepository,
        SpeakersExpensesStorage $speakersExpensesStorage
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->speakerRepository = $speakerRepository;
        $this->speakersExpensesStorage = $speakersExpensesStorage;
    }

    public function __invoke(Request $request): Response
    {
        $id = $request->query->get('id');

        $event = $this->eventActionHelper->getEventById($id);

        $speakers = $event === null ? null : $this->speakerRepository->getScheduledSpeakersByEvent($event, true);
        if (null !== $speakers) {
            $speakers = iterator_to_array($speakers->getIterator());
            foreach ($speakers as $k => $speaker) {
                $files = $this->speakersExpensesStorage->getFiles($speaker['speaker']);
                $speakers[$k]['hasExpensesFiles'] = count($files) >= 1;
            }
        }

        return $this->render('admin/event/speakers_management.html.twig', [
            'event' => $event,
            'speakers' => $speakers,
            'event_select_form' => $this->createForm(EventSelectType::class, $event)->createView(),
        ]);
    }
}
