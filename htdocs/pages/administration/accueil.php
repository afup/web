<?php

/** @var \AppBundle\Controller\LegacyController $this */

use Afup\Site\Forum\Inscriptions;
use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Association\UserMembership\StatisticsComputer;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$smarty->assign('user_label', $this->getUser()->getLabel());

$eventRepository = $this->get('ting')->get(EventRepository::class);
$ticketEventTypeRepository = $this->get('ting')->get(TicketEventTypeRepository::class);
$nextevents = $eventRepository->getNextEvents();

$cards = [];

if ($this->isGranted('ROLE_FORUM')) {
    $inscriptions = new Inscriptions($GLOBALS['AFUP_DB']);
    foreach ($nextevents as $event) {
        $inscriptionsData = $inscriptions->obtenirStatistiques($event->getId());

        $infos = [];

        if ($event->lastsOneDay()) {
            $infos['statistics']['entrées'] = $inscriptionsData['premier_jour']['inscrits'];
        } else {
            $infos['statistics']['entrées (premier jour)'] = $inscriptionsData['premier_jour']['inscrits'];
            $infos['statistics']['entrées (deuxième jour)'] = $inscriptionsData['second_jour']['inscrits'];
        }

        $infos['title'] = $event->getTitle();
        $infos['subtitle'] = "Inscriptions";

        $montantTotal = 0;
        foreach ($ticketEventTypeRepository->getTicketsByEvent($event, false) as $ticketEventType) {
            $montantTotal += $inscriptionsData['types_inscriptions']['payants'][$ticketEventType->getTicketTypeId()] * $ticketEventType->getPrice();
        }

        $infos['statistics']['montant total'] = number_format($montantTotal, 0, 0, ' ') . ' €';
        $infos['url'] = "/pages/administration/index.php?page=forum_inscriptions&id_forum=" . $event->getId();

        $cards[] = $infos;
    }
}

if ($this->isGranted(('ROLE_VEILLE'))) {
    $infos = [];
    $infos['title'] = 'Abonnements à la veille';
    $infos['statistics']['Abonnements'] = $this->get('ting')->get(TechletterSubscriptionsRepository::class)->countAllSubscriptionsWithUser();
    $infos['url'] = $this->generateUrl('admin_techletter_members');

    $cards[] = $infos;
}

if ($this->isGranted(('ROLE_ADMIN'))) {
    $infos = [];
    $infos['title'] = 'Membres';
    $statistics = $this->get(StatisticsComputer::class)->computeStatistics();
    $infos['statistics']['Personnes physiques'] = $statistics['users_count_without_companies'];
    $infos['statistics']['Personnes morales'] = $statistics['companies_count'];

    $infos['main_statistic']['label'] = 'Membres';
    $infos['main_statistic']['value'] = $statistics['users_count'];

    $infos['url'] = $this->generateUrl('admin_members_reporting');

    $cards[] = $infos;
}


$smarty->assign('cards', $cards);
