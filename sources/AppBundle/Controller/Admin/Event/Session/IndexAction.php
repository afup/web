<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Session;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\Support\EventSelectFactory;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\RoomRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Room;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class IndexAction extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly EventSelectFactory $eventSelectFactory,
        private readonly TalkRepository $talkRepository,
        private readonly RoomRepository $roomRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $event = $this->eventActionHelper->getEventById($request->query->get('id'));
        $sessions = $this->talkRepository->getByEventWithSpeakers($event, false);

        return $this->render('event/session/index.html.twig', [
            'event' => $event,
            'sessions' => $sessions,
            'event_select_form' => $this->eventSelectFactory->create($event, $request)->createView(),
            'calendar' => [
                'date' => $event->getDateStart()?->format('Y-m-d'),
                'events' => $this->calendarEvents($sessions),
                'resources' => $this->calendarResources($event),
            ],
        ]);
    }

    private function calendarResources(Event $event): array
    {
        $rooms = $this->roomRepository->getByEvent($event);
        $colors = ['#333d29', '#2f3e46', '#132a13', '#1d3557', '#006d77', '#43291f'];
        $m = count($colors);

        $resources = [];
        /** @var Room $room */
        foreach ($rooms as $i => $room) {
            $resources[] = [
                'id' => $room->getId(),
                'title' => $room->getName(),
                'eventBackgroundColor' => $colors[$i % $m],
            ];
        }

        return $resources;
    }

    private function calendarEvents(array $sessions): array
    {
        $events = [];
        foreach ($sessions as $session) {
            if (!$session->planning || !$session->room) {
                continue;
            }
            $events[] = [
                'id' => $session->planning->getId(),
                'title' => $session->talk->getTitle(),
                'start' => $session->planning->getStart()?->format(\DateTime::ATOM),
                'end' => $session->planning->getEnd()?->format(\DateTime::ATOM),
                'resourceId' => $session->room->getId(),
            ];
        }

        return $events;
    }
}
