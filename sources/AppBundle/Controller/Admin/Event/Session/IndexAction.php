<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Session;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Entity\Planning;
use AppBundle\Event\Entity\Repository\PlanningRepository;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\RoomRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Room;
use AppBundle\Event\Model\Session\CalendarEvent;
use AppBundle\Event\Model\Session\CalendarResource;
use AppBundle\Event\Model\TalkAggregate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class IndexAction extends AbstractController
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly PlanningRepository $planningRepository,
        private readonly RoomRepository $roomRepository,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
        $event = $eventSelection->event;
        $sessions = $this->planningRepository->enrichTalkAggregates(
            $this->talkRepository->getByEventWithSpeakers($event, false),
        );

        return $this->render('event/session/index.html.twig', [
            'event' => $event,
            'sessions' => $sessions,
            'event_select_form' => $eventSelection->selectForm(),
            'calendar' => [
                'date' => $event->getDateStart()?->format('Y-m-d'),
                'events' => $this->calendarEvents($sessions),
                'resources' => $this->calendarResources($event),
            ],
        ]);
    }

    /**
     * @return array<CalendarResource>
     */
    private function calendarResources(Event $event): array
    {
        $rooms = $this->roomRepository->getByEvent($event);
        $colors = ['#333d29', '#2f3e46', '#132a13', '#1d3557', '#006d77', '#43291f'];
        $m = count($colors);

        $resources = [];
        /** @var Room $room */
        foreach ($rooms as $i => $room) {
            $resources[] = new CalendarResource(
                $room->getId(),
                $room->getName(),
                $colors[$i % $m],
            );
        }

        return $resources;
    }

    /**
     * @param array<TalkAggregate> $sessions
     * @return array<CalendarEvent>
     */
    private function calendarEvents(array $sessions): array
    {
        $timezone = new \DateTimeZone(Planning::TIMEZONE);

        $events = [];
        foreach ($sessions as $session) {
            if (!$session->planning || !$session->room || !$session->planning->start || !$session->planning->end) {
                continue;
            }
            $events[] = new CalendarEvent(
                $session->planning->id,
                $session->talk->getTitle(),
                $this->formatForCalendar($session->planning->start, $timezone),
                $this->formatForCalendar($session->planning->end, $timezone),
                $session->room->getId(),
            );
        }

        return $events;
    }

    /**
     * Le calendrier ne gère pas les timezones : il interprète la date reçue telle
     * qu'elle est écrite. On lui transmet donc l'heure locale de l'événement, sans décalage.
     */
    private function formatForCalendar(\DateTimeInterface $date, \DateTimeZone $timezone): string
    {
        return \DateTimeImmutable::createFromInterface($date)
            ->setTimezone($timezone)
            ->format('Y-m-d\TH:i:s');
    }
}
