<?php


namespace AppBundle\Controller;

use Afup\Site\Forum\Inscriptions;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Form\RoomType;
use AppBundle\Event\Form\SponsorTokenType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\RoomRepository;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use AppBundle\Event\Model\Room;
use AppBundle\Event\Model\SponsorTicket;
use AppBundle\Event\Model\Ticket;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class AdminEventController extends Controller
{
    public function roomAction(Request $request)
    {
        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        $event = $this->getEvent($eventRepository, $request);

        if ($event === null) {
            return $this->createNotFoundException('Could not find event');
        }

        /**
         * @var $roomRepository RoomRepository
         */
        $roomRepository = $this->get('ting')->get(RoomRepository::class);
        $rooms = $roomRepository->getByEvent($event);
        $editForms = $this->getFormsForRooms($rooms);

        foreach ($editForms as $form) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $room = $form->getData();
                if ($request->request->has('delete')) {
                    $roomRepository->delete($room);
                    $this->addFlash('notice', sprintf('La salle "%s" a été supprimée.', $room->getName()));
                } else {
                    $roomRepository->save($room);
                    $this->addFlash('notice', sprintf('La salle "%s" a été sauvegardée.', $room->getName()));
                }
                return $this->redirectToRoute('admin_event_room', ['id' => $event->getId()]);
            }
        }

        $newRoom = new Room();
        $newRoom->setEventId($event->getId());

        $addForm = $this->createForm(RoomType::class, $newRoom);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $newRoom = $addForm->getData();
            $roomRepository->save($newRoom);
            $this->addFlash('notice', sprintf('La salle "%s" a été ajoutée.', $newRoom->getName()));
            return $this->redirectToRoute('admin_event_room', ['id' => $event->getId()]);
        }

        return $this->render(':admin/event:rooms.html.twig',
            [
                'event' => $event,
                'rooms' => $rooms,
                'addForm' => $addForm->createView(),
                'editForms' => array_map(function (Form $form) {
                    return $form->createView();
                }, $editForms),
                'title' => 'Gestion des salles'
            ]
        );
    }

    public function changeEventAction(Event $selectedEvent = null)
    {
        $form = $this->createForm(
            EventSelectType::class,
            $selectedEvent,
            ['event_repository' => $this->get('ting')->get(EventRepository::class)]
        );
        return $this->render(':admin/event:change_event.html.twig', ['form' => $form->createView()]);
    }

    public function sponsorTicketAction(Request $request)
    {
        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        $event = $this->getEvent($eventRepository, $request);

        if ($event === null) {
            return $this->createNotFoundException('Could not find event');
        }

        /**
         * @var $sponsorTicketRepository SponsorTicketRepository
         */
        $sponsorTicketRepository = $this->get('ting')->get(SponsorTicketRepository::class);

        $tokens = $sponsorTicketRepository->getBy(['idForum' => $event->getId()]);

        $edit = false;
        if ($request->query->has('ticket') === true) {
            $edit = true;

            $newToken = $sponsorTicketRepository->get($request->query->get('ticket'));
            $newToken->setEditedOn(new \DateTime());
        } else {
            $newToken = new SponsorTicket();
            $newToken
                ->setToken(base64_encode(random_bytes(30)))
                ->setIdForum($event->getId())
                ->setCreatedOn(new \DateTime())
                ->setEditedOn(new \DateTime())
                ->setCreatorId($this->getUser()->getId())
            ;
        }
        $form = $this->createForm(SponsorTokenType::class, $newToken);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($newToken->getId() === null) {
                $this->get('app.sponsor_token_mail')->sendNotification($newToken);
            }
            $sponsorTicketRepository->save($newToken);
            $this->addFlash('notice', 'Le token a été enregistré');

            return $this->redirectToRoute('admin_event_sponsor_ticket', ['id' => $event->getId()]);
        }

        return $this->render(':admin/event:sponsor_ticket.html.twig', [
            'tokens' => $tokens,
            'event' => $event,
            'title' => 'Gestion des inscriptions sponsors',
            'form' => $form->createView(),
            'edit' => $edit
        ]);
    }

    public function resendSponsorTokenAction(Request $request)
    {
        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        $event = $this->getEvent($eventRepository, $request);

        if ($event === null) {
            return $this->createNotFoundException('Could not find event');
        }
        /**
         * @var $sponsorTicketRepository SponsorTicketRepository
         */
        $sponsorTicketRepository = $this->get('ting')->get(SponsorTicketRepository::class);
        $token = $sponsorTicketRepository->get($request->request->get('sponsor_token_id'));
        if ($token === null) {
            throw $this->createNotFoundException(sprintf('Could not find token with id: %s', $request->request->get('sponsor_token_id')));
        }

        $this->get('app.sponsor_token_mail')->sendNotification($token);

        $this->addFlash('notice', 'Le mail a été renvoyé');

        return $this->redirectToRoute('admin_event_sponsor_ticket', ['id' => $event->getId()]);
    }

    public function statsAction(Request $request)
    {
        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        $event = $this->getEvent($eventRepository, $request);

        /**
         * @var $legacyInscriptions Inscriptions
         */
        $legacyInscriptions = $this->get('app.legacy_model_factory')->createObject(Inscriptions::class);

        $stats = $legacyInscriptions->obtenirSuivi($event->getId());

        $ticketsDayOne = $this->get('app.ticket_repository')->getPublicSoldTicketsByDay(Ticket::DAY_ONE, $event);
        $ticketsDayTwo = $this->get('app.ticket_repository')->getPublicSoldTicketsByDay(Ticket::DAY_TWO, $event);

        $ticketTypes = [];
        /**
         * @var $ticketTypeRepository TicketTypeRepository
         */
        $ticketTypeRepository = $this->get('ting')->get(TicketTypeRepository::class);

        $chart = [
            'chart' => [
                'renderTo' => 'container',
                'zoomType' => 'x',
                'spacingRight' => 20
            ],
            'title' => ['text' => 'Evolution des inscriptions'],
            'subtitle' => ['text' => 'Cliquez/glissez dans la zone pour zoomer'],
            'xAxis' => [
                'type' => 'linear',
                'title' => ['text' => null],
                'allowDecimals' => false
            ],
            'yAxis' => [
                'title' => ['text' => 'Inscriptions'],
                'min' => 0,
                'startOnTick' => false,
                'showFirstLabel' => false
            ],
            'tooltip' => ['shared' => true],
            'legend' => ['enabled' => true],
            'series' => [
                [
                    'name' => $event->getTitle(),
                    'data' => array_values(array_map(function ($item) {
                        return $item['n'];
                    }, $stats['suivi']))
                ],
                [
                    'name' => 'n-1',
                    'data' => array_values(array_map(function ($item) {
                        return $item['n_1'];
                    }, $stats['suivi']))
                ]
            ]
        ];

        $rawStatsByType = $legacyInscriptions->obtenirStatistiques($event->getId())['types_inscriptions']['payants'];
        $totalInscrits = array_sum($rawStatsByType);
        array_walk($rawStatsByType, function (&$item, $key) use (&$ticketTypes, $totalInscrits, $ticketTypeRepository) {
            if (isset($ticketTypes[$key]) === false) {
                $type = $ticketTypeRepository->get($key);
                $ticketTypes[$key] = $type->getPrettyName();
            }
            $item = ['name' => $ticketTypes[$key], 'y' => $item / $totalInscrits ];
        });

        $rawStatsByType = array_values($rawStatsByType);

        $pieChartConf = [
            "chart" => [
                "plotBackgroundColor" => null,
                "plotBorderWidth" => null,
                "plotShadow" => false,
                "type" => 'pie'
            ],
            "title" => [
                "text" => 'Répartition des types d\'inscriptions payantes'
            ],
            "tooltip" => [
                "pointFormat" => '{series.name}: <b>{point.percentage:.1f}%</b>'
            ],
            "plotOptions" => [
                "pie" => [
                    "allowPointSelect" => true,
                    "cursor" => 'pointer',
                    "dataLabels" => [
                        "enabled" => true,
                        "format" => '<b>{point.name}</b>: {point.percentage:.1f} %',
                        "style" => [
                            "color" => 'black'
                        ]
                    ]
                ]
            ],
            "series" => [[
                "name" => 'Inscriptions',
                "colorByPoint" => true,
                "data" => $rawStatsByType
            ]]
        ];

        return $this->render(':admin/event:stats.html.twig', [
            'title' => 'Suivi inscriptions',
            'event' => $event,
            'chartConf' => $chart,
            'pieChartConf' => $pieChartConf,
            'stats' => $stats,
            'seats' => [
                'available' => $event->getSeats(),
                'one' => $ticketsDayOne,
                'two' => $ticketsDayTwo
            ]
        ]);
    }

    /**
     * @param CollectionInterface $rooms
     * @return Form[]
     */
    private function getFormsForRooms(CollectionInterface $rooms)
    {
        $forms = [];
        foreach ($rooms as $room) {
            $forms[] = $this->get('form.factory')->createNamedBuilder('edit_room_' . $room->getId(), RoomType::class, $room)->getForm();
        }
        return $forms;
    }

    private function getEvent(EventRepository $eventRepository, Request $request)
    {
        $event = null;
        if ($request->query->has('id') === false) {
            $event = $eventRepository->getNextEvent();
            $event = $eventRepository->get($event->getId());
        } else {
            $id = $request->query->getInt('id');
            $event = $eventRepository->get($id);
        }

        return $event;
    }
}
