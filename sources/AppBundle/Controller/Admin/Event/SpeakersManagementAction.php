<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\Support\EventSelectFactory;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\SpeakerInfos\SpeakersExpensesStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpeakersManagementAction extends AbstractController implements AdminActionWithEventSelector
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly SpeakerRepository $speakerRepository,
        private readonly SpeakersExpensesStorage $speakersExpensesStorage,
        private readonly EventSelectFactory $eventSelectFactory,
    ) {}

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
            'event_select_form' => $this->eventSelectFactory->create($event, $request)->createView(),
        ]);
    }
}
