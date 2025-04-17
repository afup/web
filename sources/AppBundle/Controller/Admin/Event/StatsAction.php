<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use Afup\Site\Forum\Inscriptions;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\EventCompareSelectType;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\EventStatsRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use AppBundle\Event\Model\Ticket;
use AppBundle\LegacyModelFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StatsAction extends AbstractController
{
    private EventActionHelper $eventActionHelper;
    private LegacyModelFactory $legacyModelFactory;
    private TicketRepository $ticketRepository;
    private TicketTypeRepository $ticketTypeRepository;
    private EventStatsRepository $eventStatsRepository;
    private EventRepository $eventRepository;

    public function __construct(
        EventActionHelper $eventActionHelper,
        LegacyModelFactory $legacyModelFactory,
        TicketRepository $ticketRepository,
        TicketTypeRepository $ticketTypeRepository,
        EventStatsRepository $eventStatsRepository,
        EventRepository $eventRepository
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->legacyModelFactory = $legacyModelFactory;
        $this->ticketRepository = $ticketRepository;
        $this->ticketTypeRepository = $ticketTypeRepository;
        $this->eventStatsRepository = $eventStatsRepository;
        $this->eventRepository = $eventRepository;
    }

    public function __invoke(Request $request): Response
    {
        $event = $this->eventActionHelper->getEventById($request->query->get('event_id'));
        if ($comparedEventId = $request->query->get('compared_event_id')) {
            $comparedEvent = $this->eventActionHelper->getEventById($comparedEventId, false);
        } else {
            $comparedEvent = $this->eventRepository->getLastYearEvent($event);
        }

        $comparedEventForm = $this->createForm(EventCompareSelectType::class, [
            'event_id' => $event->getId(),
            'compared_event_id' => $comparedEvent->getId(),
        ], [
            'events' => $this->eventRepository->getAll()
        ]);

        $legacyInscriptions = $this->legacyModelFactory->createObject(Inscriptions::class);
        $stats = $legacyInscriptions->obtenirSuivi($event->getId(), $comparedEvent->getId());
        $ticketsDayOne = $this->ticketRepository->getPublicSoldTicketsByDay(Ticket::DAY_ONE, $event);
        $ticketsDayTwo = $this->ticketRepository->getPublicSoldTicketsByDay(Ticket::DAY_TWO, $event);

        $ticketTypes = [];

        $chart = [
            'chart' => [
                'renderTo' => 'container',
                'zoomType' => 'x',
                'spacingRight' => 20,
            ],
            'title' => ['text' => 'Evolution des inscriptions'],
            'subtitle' => ['text' => 'Cliquez/glissez dans la zone pour zoomer'],
            'xAxis' => [
                'type' => 'linear',
                'title' => ['text' => null],
                'allowDecimals' => false,
            ],
            'yAxis' => [
                'title' => ['text' => 'Inscriptions'],
                'min' => 0,
                'startOnTick' => false,
                'showFirstLabel' => false,
            ],
            'tooltip' => ['shared' => true],
            'legend' => ['enabled' => true],
            'series' => [
                [
                    'name' => $event->getTitle(),
                    'data' => array_values(array_map(static fn ($item) => $item['n'], $stats['suivi'])),
                ],
                [
                    'name' => $comparedEvent->getTitle(),
                    'data' => array_values(array_map(static fn ($item) => $item['n_1'], $stats['suivi'])),
                ],
            ],
        ];

        $rawStatsByType = $this->eventStatsRepository->getStats($event->getId())->ticketType->paying;
        $totalInscrits = array_sum($rawStatsByType);
        array_walk($rawStatsByType, function (&$item, $key) use (&$ticketTypes, $totalInscrits): void {
            if (isset($ticketTypes[$key]) === false) {
                $type = $this->ticketTypeRepository->get($key);
                $ticketTypes[$key] = $type->getPrettyName();
            }
            $item = ['name' => $ticketTypes[$key], 'y' => $item / $totalInscrits];
        });

        $rawStatsByType = array_values($rawStatsByType);

        $pieChartConf = [
            "chart" => [
                "plotBackgroundColor" => null,
                "plotBorderWidth" => null,
                "plotShadow" => false,
                "type" => 'pie',
            ],
            "title" => [
                "text" => 'Répartition des types d\'inscriptions payantes',
            ],
            "tooltip" => [
                "pointFormat" => '{series.name}: <b>{point.percentage:.1f}%</b>',
            ],
            "plotOptions" => [
                "pie" => [
                    "allowPointSelect" => true,
                    "cursor" => 'pointer',
                    "dataLabels" => [
                        "enabled" => true,
                        "format" => '<b>{point.name}</b>: {point.percentage:.1f} %',
                        "style" => [
                            "color" => 'black',
                        ],
                    ],
                ],
            ],
            "series" => [
                [
                    "name" => 'Inscriptions',
                    "colorByPoint" => true,
                    "data" => $rawStatsByType,
                ],
            ],
        ];

        return $this->render('admin/event/stats.html.twig', [
            'title' => 'Suivi inscriptions',
            'event' => $event,
            'chartConf' => $chart,
            'pieChartConf' => $pieChartConf,
            'stats' => $stats,
            'seats' => [
                'available' => $event->getSeats(),
                'one' => $ticketsDayOne,
                'two' => $ticketsDayTwo,
            ],
            'event_compare_form' => $comparedEventForm->createView(),
        ]);
    }
}
