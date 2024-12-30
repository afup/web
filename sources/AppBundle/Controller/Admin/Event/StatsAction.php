<?php

namespace AppBundle\Controller\Admin\Event;

use Afup\Site\Forum\Forum;
use Afup\Site\Forum\Inscriptions;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\EventCompareSelectType;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Model\Repository\EventStatsRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use AppBundle\Event\Model\Ticket;
use AppBundle\LegacyModelFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class StatsAction
{
    /** @var EventActionHelper */
    private $eventActionHelper;
    /** @var LegacyModelFactory */
    private $legacyModelFactory;
    /** @var TicketRepository */
    private $ticketRepository;
    /** @var TicketTypeRepository */
    private $ticketTypeRepository;
    /** @var EventStatsRepository */
    private $eventStatsRepository;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var Environment */
    private $twig;

    /**
     * @var Forum
     */
    private $forum;

    public function __construct(
        EventActionHelper $eventActionHelper,
        LegacyModelFactory $legacyModelFactory,
        TicketRepository $ticketRepository,
        TicketTypeRepository $ticketTypeRepository,
        EventStatsRepository $eventStatsRepository,
        FormFactoryInterface $formFactory,
        Forum $forum,
        Environment $twig
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->legacyModelFactory = $legacyModelFactory;
        $this->ticketRepository = $ticketRepository;
        $this->ticketTypeRepository = $ticketTypeRepository;
        $this->eventStatsRepository = $eventStatsRepository;
        $this->formFactory = $formFactory;
        $this->forum = $forum;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $id = $request->query->get('id');
        $comparedEventId = $request->query->get('compared_event_id');

        if(!$id && !$comparedEventId) {
            $compareData =  $request->query->get('compare_event');
            $id = $compareData['id'];
            $comparedEventId = $compareData['compared_event_id'];
        }

        dump($id, $comparedEventId);

        $event = $this->eventActionHelper->getEventById($id);

        $legacyInscriptions = $this->legacyModelFactory->createObject(Inscriptions::class);

        if ($comparedEventId) {
            $comparedEvent = $this->eventActionHelper->getEventById($comparedEventId, false);
            $comparedSerieName = $comparedEvent->getTitle();
        } elseif($id !== null) {
            $comparedEventId = $this->forum->obtenirForumPrecedent($id);
            $comparedEvent = $this->eventActionHelper->getEventById($comparedEventId, false);
            $comparedSerieName = $comparedEvent->getTitle();
        } else {
            $comparedSerieName = 'n-1';
        }

        $comparedEventForm = $this->formFactory->create(EventCompareSelectType::class, [
            'id' => $id,
            'compared_event_id' => $comparedEventId,
        ])->createView();

        $stats = $legacyInscriptions->obtenirSuivi($event->getId(), $comparedEventId);
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
                    'data' => array_values(array_map(static function ($item) {
                        return $item['n'];
                    }, $stats['suivi'])),
                ],
                [
                    'name' => $comparedSerieName,
                    'data' => array_values(array_map(static function ($item) {
                        return $item['n_1'];
                    }, $stats['suivi'])),
                ],
            ],
        ];

        $rawStatsByType = $this->eventStatsRepository->getStats($event->getId())->ticketType->paying;
        $totalInscrits = array_sum($rawStatsByType);
        array_walk($rawStatsByType, function (&$item, $key) use (&$ticketTypes, $totalInscrits) {
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

        return new Response($this->twig->render('admin/event/stats.html.twig', [
            'title' => 'Suivi inscriptions',
            'event' => $event,
            'chartConf' => $chart,
            'pieChartConf' => $pieChartConf,
            'stats' => $stats,
            'seats' => [
                'available' => $event === null ? null: $event->getSeats(),
                'one' => $ticketsDayOne,
                'two' => $ticketsDayTwo,
            ],
            'event_select_form' => $this->formFactory->create(EventSelectType::class, $event)->createView(),
            'event_compare_form' => $comparedEventForm,
        ]));
    }
}
