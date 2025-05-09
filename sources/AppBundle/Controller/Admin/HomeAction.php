<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin;

use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\StatisticsComputer;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\EventStatsRepository;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use Assert\Assertion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeAction extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventStatsRepository $eventStatsRepository,
        private readonly TicketEventTypeRepository $ticketEventTypeRepository,
        private readonly TechletterSubscriptionsRepository $techletterSubscriptionsRepository,
        private readonly GeneralMeetingRepository $generalMeetingRepository,
        private readonly StatisticsComputer $statisticsComputer,
    ) {
    }

    public function __invoke(): Response
    {
        $nextevents = $this->eventRepository->getNextEvents();
        $cards = [];
        if ($this->isGranted('ROLE_FORUM') && $nextevents) {
            foreach ($nextevents as $event) {
                $stats = $this->eventStatsRepository->getStats($event->getId());
                $info = [];
                if ($event->lastsOneDay()) {
                    $info['statistics']['entrées'] = $stats->firstDay->registered;
                } else {
                    $info['statistics']['entrées (premier jour)'] = $stats->firstDay->registered;
                    $info['statistics']['entrées (deuxième jour)'] = $stats->secondDay->registered;
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
                $info['url'] = '/pages/administration/index.php?page=forum_inscriptions&id_forum=' . $event->getId();

                $cards[] = $info;
            }
        }

        if ($this->isGranted(('ROLE_ADMIN'))) {
            $cards[] = [
                'title' => 'Abonnements à la veille',
                'statistics' => ['Abonnements' => $this->techletterSubscriptionsRepository->countAllSubscriptionsWithUser()],
                'url' => $this->generateUrl('admin_techletter_members'),
            ];
        }
        /** @var User $user */
        $user = $this->getUser();
        Assertion::isInstanceOf($user, User::class);
        if ($this->isGranted(('ROLE_ADMIN'))) {
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

            $latestDate = $this->generalMeetingRepository->getLatestDate();
            if ($this->generalMeetingRepository->hasGeneralMeetingPlanned($latestDate)) {
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
            'user_label' => $user->getLabel(),
            'cards' => $cards,
        ]);
    }
}
