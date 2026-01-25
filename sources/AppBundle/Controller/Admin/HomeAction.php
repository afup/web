<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin;

use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Association\UserMembership\StatisticsComputer;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\EventStatsRepository;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use AppBundle\Security\Authentication;
use Psr\Clock\ClockInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeAction extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventStatsRepository $eventStatsRepository,
        private readonly TicketEventTypeRepository $ticketEventTypeRepository,
        private readonly TechletterSubscriptionsRepository $techletterSubscriptionsRepository,
        private readonly GeneralMeetingRepository $generalMeetingRepository,
        private readonly StatisticsComputer $statisticsComputer,
        private readonly ClockInterface $clock,
        private readonly Authentication $authentication,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {}

    public function __invoke(): Response
    {
        $nextevents = $this->eventRepository->getNextEvents();
        $cards = [];
        if ($this->isGranted('ROLE_FORUM') && $nextevents) {
            $cfp = [
                'title' => 'CFP',
                'subtitle' => 'Talks et speakers',
                'url' => $this->urlGenerator->generate('admin_talk_list'),
                'statistics' => [],
            ];

            /** @var Event $event */
            foreach ($nextevents as $event) {
                $stats = $this->eventStatsRepository->getStats((int) $event->getId());
                $info = [];
                if ($event->lastsOneDay()) {
                    $ticketsLabel = 'entrées';
                    $tickets = $stats->firstDay->confirmed + $stats->firstDay->pending;
                    if ($event->getSeats()) {
                        $percentage = floor(($tickets * 100) / $event->getSeats());
                        $tickets = $tickets . ' / ' . $event->getSeats();

                        $ticketsLabel = "entrées ($percentage%)";
                    }

                    $info['statistics'][$ticketsLabel] = $tickets;
                } else {
                    $info['statistics']['entrées (premier jour)'] = $stats->firstDay->confirmed + $stats->firstDay->pending;
                    $info['statistics']['entrées (deuxième jour)'] = $stats->secondDay->confirmed + $stats->secondDay->pending;
                }
                $info['title'] = $event->getTitle();
                $info['subtitle'] = 'Inscriptions';

                $montantTotal = 0;
                foreach ($this->ticketEventTypeRepository->getTicketsByEvent($event, false) as $ticketEventType) {
                    if (array_key_exists($ticketEventType->getTicketTypeId(), $stats->ticketType->paying)) {
                        $montantTotal += $stats->ticketType->paying[$ticketEventType->getTicketTypeId()] * $ticketEventType->getPrice();
                    }
                }

                $info['statistics']['montant total'] = number_format($montantTotal, 0, ',', "\u{a0}") . "\u{a0}€";
                $info['url'] = $this->generateUrl('admin_event_ticket_list', ['id' => $event->getId()]);

                $cards[] = $info;

                // Les stats du CFP sont affichés pendant un certain temps après la date de fin de l'appel
                $dateEndCallForPapers = $event->getDateEndCallForPapers();
                if ($dateEndCallForPapers && $dateEndCallForPapers < $this->clock->now()->add(new \DateInterval('P3M'))) {
                    $cfp['statistics'][$event->getTitle()] = [
                        [
                            'icon' => 'microphone',
                            'value' => $stats->cfp->talks,
                        ],
                        [
                            'icon' => 'user',
                            'value' => $stats->cfp->speakers,
                        ],
                    ];
                }
            }

            if (count($cfp['statistics']) > 0) {
                $cards[] = $cfp;
            }
        }

        if ($this->isGranted(('ROLE_ADMIN'))) {
            $cards[] = [
                'title' => 'Abonnements à la veille',
                'statistics' => ['Abonnements' => $this->techletterSubscriptionsRepository->countAllSubscriptionsWithUser()],
                'url' => $this->generateUrl('admin_techletter_members'),
            ];

            $statistics = $this->statisticsComputer->computeStatistics();
            $cards[] = [
                'title' => 'Membres',
                'statistics' => [
                    'Personnes physiques' => $statistics->usersCountWithoutCompanies,
                    'Personnes morales' => $statistics->companiesCount,
                ],
                'main_statistic' => [
                    'label' => 'Membres',
                    'value' => $statistics->usersCount,
                ],
                'url' => $this->generateUrl('admin_members_reporting'),
            ];

            $latestDate = $this->generalMeetingRepository->getLatestGeneralAssemblyDate();
            if ($this->generalMeetingRepository->hasGeneralMeetingPlanned()) {
                $cards[] = [
                    'title' => 'Assemblée générale',
                    'statistics' => [
                        'Votes et pouvoirs' => $this->generalMeetingRepository->countAttendeesAndPowers($latestDate),
                        'Présences' => $this->generalMeetingRepository->countAttendees($latestDate),
                    ],
                    'main_statistic' => [
                        'label' => 'Quorum',
                        'value' => $this->generalMeetingRepository->obtenirEcartQuorum($latestDate, $statistics->usersCount),
                    ],
                    'url' => $this->generateUrl('admin_members_general_meeting'),
                ];
            }
        }

        return $this->render('admin/home.html.twig', [
            'user_label' => $this->authentication->getAfupUser()->getLabel(),
            'cards' => $cards,
        ]);
    }
}
