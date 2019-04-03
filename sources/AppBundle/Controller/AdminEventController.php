<?php


namespace AppBundle\Controller;

use Afup\Site\Forum\Facturation;
use Afup\Site\Forum\Inscriptions;
use AppBundle\Email\Emails;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Form\RoomType;
use AppBundle\Event\Form\SponsorTokenType;
use AppBundle\Event\Form\TicketSpecialPriceType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\RoomRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Repository\TicketSpecialPriceRepository;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use AppBundle\Event\Model\Room;
use AppBundle\Event\Model\SponsorTicket;
use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Model\TicketSpecialPrice;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\KernelEvents;

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
            throw $this->createNotFoundException('Could not find event');
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


    public function specialPriceAction(Request $request)
    {
        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);

        if (null === ($event = $this->getEvent($eventRepository, $request))) {
            throw $this->createNotFoundException('Could not find event');
        }

        $ticketSpecialPriceRepository = $this->get('ting')->get(TicketSpecialPriceRepository::class);

        $specialPrice = new TicketSpecialPrice();
        $specialPrice
            ->setToken(base64_encode(random_bytes(30)))
            ->setEventId($event->getId())
            ->setDateStart(new \DateTime())
            ->setDateEnd($event->getDateEndSales())
            ->setCreatedOn(new \DateTime())
            ->setCreatorId($this->getUser()->getId())
        ;

        $form = $this->createForm(TicketSpecialPriceType::class, $specialPrice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticketSpecialPriceRepository->save($form->getData());
            $this->addFlash('notice', 'Le token a été enregistré');

            return $this->redirectToRoute('admin_event_special_price', ['id' => $event->getId()]);
        }

        return $this->render(':admin/event:special_price.html.twig', [
            'special_prices' => $ticketSpecialPriceRepository->getByEvent($event),
            'event' => $event,
            'title' => 'Gestion des prix custom',
            'form' => $form->createView(),
        ]);
    }

    public function speakersManagementAction(Request $request)
    {
        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);

        if (null === ($event = $this->getEvent($eventRepository, $request))) {
            throw $this->createNotFoundException('Could not find event');
        }

        /**
         * @var SpeakerRepository $speakersRepository
         */
        $speakersRepository = $this->get('ting')->get(SpeakerRepository::class);

        $speakers = $speakersRepository->getScheduledSpeakersByEvent($event, true);

        return $this->render(':admin/event:speakers_management.html.twig', [
            'event' => $event,
            'title' => 'Gestion documentaire des speakers',
            'speakers' => $speakers,
        ]);
    }

    public function sponsorTicketAction(Request $request)
    {
        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        $event = $this->getEvent($eventRepository, $request);

        if ($event === null) {
            throw $this->createNotFoundException('Could not find event');
        }

        /**
         * @var $sponsorTicketRepository SponsorTicketRepository
         */
        $sponsorTicketRepository = $this->get('ting')->get(SponsorTicketRepository::class);

        $tokens = $sponsorTicketRepository->getByEvent($event);

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
                $this->get(\AppBundle\Event\Ticket\SponsorTokenMail::class)->sendNotification($newToken);
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
            throw $this->createNotFoundException('Could not find event');
        }
        /**
         * @var $sponsorTicketRepository SponsorTicketRepository
         */
        $sponsorTicketRepository = $this->get('ting')->get(SponsorTicketRepository::class);
        $token = $sponsorTicketRepository->get($request->request->get('sponsor_token_id'));
        if ($token === null) {
            throw $this->createNotFoundException(sprintf('Could not find token with id: %s', $request->request->get('sponsor_token_id')));
        }

        $this->get(\AppBundle\Event\Ticket\SponsorTokenMail::class)->sendNotification($token);

        $this->addFlash('notice', 'Le mail a été renvoyé');

        return $this->redirectToRoute('admin_event_sponsor_ticket', ['id' => $event->getId()]);
    }

    public function sendLastCallSponsorTokenAction(Request $request)
    {
        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        $event = $this->getEvent($eventRepository, $request);

        if ($event === null) {
            throw $this->createNotFoundException('Could not find event');
        }
        /**
         * @var $sponsorTicketRepository SponsorTicketRepository
         */
        $sponsorTicketRepository = $this->get('ting')->get(SponsorTicketRepository::class);

        /**
         * @var $tokens SponsorTicket[]
         */
        $tokens = $sponsorTicketRepository->getByEvent($event);

        $mailSent = 0;

        foreach ($tokens as $token) {
            if ($token->getPendingInvitations() > 0) {
                $mailSent++;
                $this->get(\AppBundle\Event\Ticket\SponsorTokenMail::class)->sendNotification($token, true);
            }
        }

        $this->addFlash('notice', sprintf('%s mails de relance ont été envoyés', $mailSent));

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
        $legacyInscriptions = $this->get(\AppBundle\LegacyModelFactory::class)->createObject(Inscriptions::class);

        $stats = $legacyInscriptions->obtenirSuivi($event->getId());

        $ticketsDayOne = $this->get(\AppBundle\Event\Model\Repository\TicketRepository::class)->getPublicSoldTicketsByDay(Ticket::DAY_ONE, $event);
        $ticketsDayTwo = $this->get(\AppBundle\Event\Model\Repository\TicketRepository::class)->getPublicSoldTicketsByDay(Ticket::DAY_TWO, $event);

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

    public function exportAnonymousDataAction(Request $request)
    {
        if ($request->getMethod() === Request::METHOD_POST) {
            if ($this->isCsrfTokenValid('event_anonymous_export', $request->request->get('token')) === false) {
                $this->addFlash('error', 'Token invalide');
            } else {
                $data = $this->get(\AppBundle\Event\AnonymousExport::class)->exportData();

                $response = new StreamedResponse(function () use ($data) {
                    $handle = fopen('php://output', 'w+');
                    // Nom des colonnes du CSV
                    fputcsv($handle, ['Label',
                        'Event'
                    ], ';');

                    //Champs
                    foreach ($data as $row) {
                        fputcsv($handle, [$row['label'],
                            $row['event']
                        ], ';');
                    }

                    fclose($handle);
                });

                $response->setStatusCode(200);
                $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
                $response->headers->set('Content-Disposition', 'attachment; filename="inscriptions.csv"');

                return $response;
            }
        }
        return $this->render(':admin/event:export_anonymous.html.twig', [
            'title' => 'Export anonymisé des données d\'inscriptions',
            'token' => $this->get('security.csrf.token_manager')->getToken('event_anonymous_export')
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function pendingBankwiresAction(Request $request)
    {
        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        $event = $this->getEvent($eventRepository, $request);

        if ($event === null) {
            throw $this->createNotFoundException('Could not find event');
        }

        $invoiceRepository = $this->get(\AppBundle\Event\Model\Repository\InvoiceRepository::class);

        if ($request->getMethod() === Request::METHOD_POST) {
            if ($this->isCsrfTokenValid('admin_event_bankwires', $request->request->get('token')) === false) {
                $this->addFlash('error', 'Erreur de token CSRF, veuillez réessayer');
            } else {
                $reference = $request->request->get('bankwireReceived');
                $invoice = $this->get(\AppBundle\Event\Model\Repository\InvoiceRepository::class)->getByReference($reference);
                if ($invoice === null) {
                    throw $this->createNotFoundException(sprintf('No invoice with this reference: "%s"', $reference));
                }
                $this->setInvoicePaid($event, $invoice);
            }
        }

        $pendingBankwires = $invoiceRepository->getPendingBankwires($event);

        return $this->render(':admin/event:bankwires.html.twig', [
            'pendingBankwires' => $pendingBankwires,
            'event' => $event,
            'title' => 'Virements en attente',
            'token' => $this->get('security.csrf.token_manager')->getToken('admin_event_bankwires')
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

    private function setInvoicePaid(Event $event, Invoice $invoice)
    {
        $invoice
            ->setStatus(Ticket::STATUS_PAID)
            ->setPaymentDate(new \DateTime())
        ;
        $this->get(\AppBundle\Event\Model\Repository\InvoiceRepository::class)->save($invoice);
        $tickets = $this->get(\AppBundle\Event\Model\Repository\TicketRepository::class)->getByReference($invoice->getReference());

        /**
         * @var $forumFacturation Facturation
         */
        $forumFacturation = $this->get(\AppBundle\LegacyModelFactory::class)->createObject(Facturation::class);
        $forumFacturation->envoyerFacture($invoice->getReference());

        $this->addFlash('notice', sprintf('La facture %s a été marquée comme payée', $invoice->getReference()));

        $mailer = $this->get(\Afup\Site\Utils\Mail::class);
        $logger = $this->get('logger');
        foreach ($tickets as $ticket) {
            /**
             * @var $ticket Ticket
             */
            $ticket
                ->setStatus(Ticket::STATUS_PAID)
                ->setInvoiceStatus(Ticket::INVOICE_SENT)
            ;
            $this->get(\AppBundle\Event\Model\Repository\TicketRepository::class)->save($ticket);

            $this->get('event_dispatcher')->addListener(KernelEvents::TERMINATE, function () use ($event, $ticket, $mailer, $logger) {
                $this->get(\AppBundle\Email\Emails::class)->sendInscription($event, $ticket->getEmail(), $ticket->getLabel());
                return 1;
            });
        }
    }

    public function badgesGenerateAction(Request $request)
    {
        $event = $this->getEvent($this->get(\AppBundle\Event\Model\Repository\EventRepository::class), $request);

        $file = new \SplFileObject(sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('badges_'), 'w+');
        $this->get(\AppBundle\Event\Ticket\RegistrationsExportGenerator::class)->export($event, $file);

        $headers = [
            'Content-Type' =>  'text/html; charset=utf-8',
            'Content-Disposition' => sprintf('attachment; filename="inscriptions_%s_%s.csv"', $event->getPath(), date('Ymd-His')),
        ];

        $response = new BinaryFileResponse($file, BinaryFileResponse::HTTP_OK, $headers);
        $response->deleteFileAfterSend(true);
        return $response;
    }

    /**
     * @param Request $request
     *
     * @return BinaryFileResponse
     */
    public function previousRegistrationsAction(Request $request)
    {
        $file = new \SplFileObject(sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('inscrits_'), 'w+');

        $headers = [
            'Content-Type' =>  'text/csv; charset=utf-8',
            'Content-Disposition' => sprintf('attachment; filename="inscriptions_%d_derniers_events.csv"', date('Ymd-His')),
        ];

        $events = $this->get('ting')->get(EventRepository::class)->getPreviousEvents($request->query->getInt('event_count', 4));

        $registrations = $this->get('ting')->get(TicketRepository::class)->getRegistrationsForEventsWithNewsletterAllowed($events);

        foreach ($registrations as $registration) {
            $file->fputcsv($registration);
        }

        $response = new BinaryFileResponse($file, BinaryFileResponse::HTTP_OK, $headers);
        $response->deleteFileAfterSend(true);

        return $response;
    }

    public function speakerInfosAction(Request $request)
    {
        $ting = $this->container->get('ting');

        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $ting->get(EventRepository::class);
        $event = $this->getEvent($eventRepository, $request);

        /**
         * @var $speakerRepository SpeakerRepository
         */
        $speakerRepository = $ting->get(SpeakerRepository::class);
        $speaker = $speakerRepository->get($request->get('speaker_id'));

        $controller = new SpeakerController();
        $controller->setContainer($this->container);

        return $controller->internalSpeakerPageAction($request, $event, $speaker);
    }

    public function sendTestInscriptionEmailAction(Request $request)
    {
        $ting = $this->container->get('ting');

        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $ting->get(EventRepository::class);
        $event = $this->getEvent($eventRepository, $request);

        $this->get(\AppBundle\Email\Emails::class)->sendInscription($event, Emails::EMAIL_BUREAU_ADDRESS, Emails::EMAIL_BUREAU_LABEL);
        $this->addFlash('notice', 'Mail de test envoyé');

        $url = $this->get(\AppBundle\Routing\LegacyRouter::class)->getAdminUrl('forum_gestion', ['action' => 'modifier', 'id' => $event->getId()]);

        return $this->redirect($url);
    }
}
