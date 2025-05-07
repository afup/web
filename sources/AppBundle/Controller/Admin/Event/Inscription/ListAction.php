<?php

namespace AppBundle\Controller\Admin\Event\Inscription;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\EventStatsRepository;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Ticket\TicketTypeAvailability;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListAction extends AbstractController
{
    public function __invoke(
        Request $request,
        EventActionHelper $eventActionHelper,
        EventRepository $eventRepository,
        TicketEventTypeRepository $ticketEventTypeRepository,
        TicketTypeAvailability $ticketTypeAvailability,
        EventStatsRepository $eventStatsRepository,
        TicketRepository $ticketRepository,
    ): Response
    {
        $id = $request->query->get('id');
        $direction = $request->query->get('direction');
        $sort = $request->query->get('sort');
        $filter = $request->query->get('filter');

        $event = $id ? $eventActionHelper->getEventById($id) : $eventRepository->getLastEvent();

        $membersTicket = [];

        $restantes = $this->updateGlobalsForTarif($eventRepository, $ticketEventTypeRepository, $ticketTypeAvailability, $event->getId(), $membersTicket)['restantes'];

        $stats = $eventStatsRepository->getStats($event->getId());

        return $this->render('event/inscription/list.html.twig', [
            'filter' => $filter,
            'direction' => $direction,
            'sort' => $sort,
            'forumTarifsMembers' => $membersTicket,
            'now' => new DateTime(),
            'inscriptions' => $ticketRepository->getTicketsForList($event, $filter, $sort, $direction),
            'restantes' => $restantes,
            'statistiques' => [
                'premier_jour' => [
                    'inscrits' => $stats->firstDay->registered,
                    'confirmes' => $stats->firstDay->confirmed,
                    'en_attente_de_reglement' => $stats->firstDay->pending,
                ],
                'second_jour' => [
                    'inscrits' => $stats->secondDay->registered,
                    'confirmes' => $stats->secondDay->confirmed,
                    'en_attente_de_reglement' => $stats->secondDay->pending,
                ],
                'types_inscriptions' => [
                    'confirmes' => $stats->ticketType->confirmed,
                    'inscrits' => $stats->ticketType->registered,
                    'payants' => $stats->ticketType->paying,
                ],
            ],
            'forumTarifsLib' => [
                AFUP_FORUM_INVITATION => 'Invitation',
                AFUP_FORUM_ORGANISATION => 'Organisation',
                AFUP_FORUM_PROJET => 'Projet PHP',
                AFUP_FORUM_SPONSOR => 'Sponsor',
                AFUP_FORUM_PRESSE => 'Presse',
                AFUP_FORUM_PROF => 'Enseignement supérieur',
                AFUP_FORUM_CONFERENCIER => 'Conferencier',
                AFUP_FORUM_PREMIERE_JOURNEE => 'Jour 1 ',
                AFUP_FORUM_DEUXIEME_JOURNEE => 'Jour 2',
                AFUP_FORUM_2_JOURNEES => '2 Jours',
                AFUP_FORUM_2_JOURNEES_AFUP => '2 Jours AFUP',
                AFUP_FORUM_PREMIERE_JOURNEE_AFUP => 'Jour 1 AFUP',
                AFUP_FORUM_DEUXIEME_JOURNEE_AFUP => 'Jour 2 AFUP',
                AFUP_FORUM_2_JOURNEES_ETUDIANT => '2 Jours Etudiant',
                AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT => 'Jour 1 Etudiant',
                AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT => 'Jour 2 Etudiant',
                AFUP_FORUM_2_JOURNEES_PREVENTE => '2 Jours prévente',
                AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE => '2 Jours AFUP prévente',
                AFUP_FORUM_2_JOURNEES_PREVENTE_ADHESION => '2 Jours prévente + adhésion',
                AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE => '2 Jours Etudiant prévente',
                AFUP_FORUM_2_JOURNEES_COUPON => '2 Jours avec coupon de réduction',
                AFUP_FORUM_2_JOURNEES_SPONSOR => '2 Jours par Sponsor',
                AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT_PREVENTE => '',
                AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT_PREVENTE => '',
                AFUP_FORUM_SPECIAL_PRICE => 'Tarif Spécial',
            ],
            'forumTarifs' => [
                AFUP_FORUM_INVITATION => 0,
                AFUP_FORUM_ORGANISATION => 0,
                AFUP_FORUM_SPONSOR => 0,
                AFUP_FORUM_PRESSE => 0,
                AFUP_FORUM_CONFERENCIER => 0,
                AFUP_FORUM_PROJET => 0,
                AFUP_FORUM_PROF => 0,
                AFUP_FORUM_PREMIERE_JOURNEE => 150,
                AFUP_FORUM_DEUXIEME_JOURNEE => 150,
                AFUP_FORUM_2_JOURNEES => 250,
                AFUP_FORUM_2_JOURNEES_AFUP => 150,
                AFUP_FORUM_PREMIERE_JOURNEE_AFUP => 100,
                AFUP_FORUM_DEUXIEME_JOURNEE_AFUP => 100,
                AFUP_FORUM_2_JOURNEES_ETUDIANT => 150,
                AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT => 100,
                AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT => 100,
                AFUP_FORUM_2_JOURNEES_PREVENTE => 150,
                AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE => 150,
                AFUP_FORUM_2_JOURNEES_PREVENTE_ADHESION => 150,
                AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT_PREVENTE => 100,
                AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT_PREVENTE => 100,
                AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE => 150,
                AFUP_FORUM_2_JOURNEES_COUPON => 200,
                AFUP_FORUM_2_JOURNEES_SPONSOR => 200,
                AFUP_FORUM_SPECIAL_PRICE => 0,
            ],
            'event' => $event,
            'event_select_form' => $this->createForm(EventSelectType::class, $event)->createView(),
        ]);
    }

    private function updateGlobalsForTarif(
        EventRepository $eventRepository,
        TicketEventTypeRepository $ticketEventTypeRepository,
        TicketTypeAvailability $ticketTypeAvailability,
        $forumId,
        &$membersTickets = []
    ): array {
        global $AFUP_Tarifs_Forum, $AFUP_Tarifs_Forum_Lib;
        $event = $eventRepository->get($forumId);
        $ticketTypes = $ticketEventTypeRepository->getTicketsByEvent($event, false);
        $AFUP_Tarifs_Forum_Restantes = [];

        foreach ($ticketTypes as $ticketType) {
            /**
             * @var $ticketType \AppBundle\Event\Model\TicketEventType
             */
            $AFUP_Tarifs_Forum[$ticketType->getTicketTypeId()] = $ticketType->getPrice();
            $AFUP_Tarifs_Forum_Lib[$ticketType->getTicketTypeId()] = $ticketType->getTicketType()->getPrettyName();
            $AFUP_Tarifs_Forum_Restantes[$ticketType->getTicketTypeId()] = $ticketTypeAvailability->getStock($ticketType, $event);

            if ($ticketType->getTicketType()->getIsRestrictedToMembers()) {
                $membersTickets[] = $ticketType->getTicketTypeId();
            }
        }

        return ['restantes' => $AFUP_Tarifs_Forum_Restantes];
    }
}
