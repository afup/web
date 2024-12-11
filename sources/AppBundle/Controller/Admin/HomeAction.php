<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\StatisticsComputer;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\EventStatsRepository;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use Assert\Assertion;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class HomeAction
{
    /** @var EventRepository */
    private $eventRepository;
    /** @var EventStatsRepository */
    private $eventStatsRepository;
    /** @var TicketEventTypeRepository */
    private $ticketEventTypeRepository;
    /** @var TechletterSubscriptionsRepository */
    private $techletterSubscriptionsRepository;
    /** @var StatisticsComputer */
    private $statisticsComputer;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var Security */
    private $security;
    /** @var Environment */
    private $twig;
    /** @var GeneralMeetingRepository */
    private $generalMeetingRepository;

    public function __construct(
        EventRepository $eventRepository,
        EventStatsRepository $eventStatsRepository,
        TicketEventTypeRepository $ticketEventTypeRepository,
        TechletterSubscriptionsRepository $techletterSubscriptionsRepository,
        GeneralMeetingRepository $generalMeetingRepository,
        StatisticsComputer $statisticsComputer,
        UrlGeneratorInterface $urlGenerator,
        Security $security,
        Environment $twig
    ) {
        $this->eventRepository = $eventRepository;
        $this->eventStatsRepository = $eventStatsRepository;
        $this->ticketEventTypeRepository = $ticketEventTypeRepository;
        $this->techletterSubscriptionsRepository = $techletterSubscriptionsRepository;
        $this->statisticsComputer = $statisticsComputer;
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->twig = $twig;
        $this->generalMeetingRepository = $generalMeetingRepository;
    }

    public function __invoke()
    {
        $nextevents = $this->eventRepository->getNextEvents();
        $cards = [];
        if ($this->security->isGranted('ROLE_FORUM') && $nextevents) {
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

                $info['statistics']['montant total'] = number_format($montantTotal, 0, ',', ' ') . ' €';
                $info['url'] = '/pages/administration/index.php?page=forum_inscriptions&id_forum=' . $event->getId();

                $cards[] = $info;
            }
        }

        if ($this->security->isGranted(('ROLE_ADMIN'))) {
            $cards[] = [
                'title' => 'Abonnements à la veille',
                'statistics' => ['Abonnements' => $this->techletterSubscriptionsRepository->countAllSubscriptionsWithUser()],
                'url' => $this->urlGenerator->generate('admin_techletter_members'),
            ];
        }
        /** @var User $user */
        $user = $this->security->getUser();
        Assertion::isInstanceOf($user, User::class);
        if ($this->security->isGranted(('ROLE_ADMIN'))) {
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
                'url' => $this->urlGenerator->generate('admin_members_reporting'),
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
                    'url' => $this->urlGenerator->generate('admin_members_general_meeting'),
                ];
            }
        }

        return new Response($this->twig->render('admin/home.html.twig', [
            'user_label' => $user->getLabel(),
            'cards' => $cards,
        ]));
    }
}
