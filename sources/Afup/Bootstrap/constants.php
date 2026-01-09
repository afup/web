<?php

declare(strict_types=1);

use AppBundle\Event\Model\Ticket;

date_default_timezone_set('Europe/Paris');

$root = dirname(__DIR__, 3);

if (!defined('AFUP_CHEMIN_RACINE')) {
    define('AFUP_CHEMIN_RACINE', $root . '/htdocs/');
}

const AFUP_COTISATION_PERSONNE_PHYSIQUE = 30;
const AFUP_COTISATION_PERSONNE_MORALE = 150;
const AFUP_PERSONNE_MORALE_SEUIL = 3;

const AFUP_RAISON_SOCIALE = 'AFUP';
const AFUP_ADRESSE = "32, Boulevard de Strasbourg\nCS 30108";
const AFUP_CODE_POSTAL = '75468';
const AFUP_VILLE = 'Paris Cedex 10';
const AFUP_EMAIL = 'bureau@afup.org';
const AFUP_SIRET = '500 869 011 00022';
const AFUP_NUMERO_TVA = 'FR27 500 869 011';

// Ticket transport distance
const AFUP_TRANSPORT_DISTANCE_0 = 0;
const AFUP_TRANSPORT_DISTANCE_25_50 = 25;
const AFUP_TRANSPORT_DISTANCE_50_100 = 50;
const AFUP_TRANSPORT_DISTANCE_100_500 = 100;
const AFUP_TRANSPORT_DISTANCE_500_1000 = 500;
const AFUP_TRANSPORT_DISTANCE_1000 = 1000;

// Ticket transport mode
const AFUP_TRANSPORT_MODE_SEUL_THERMIQUE = 10;
const AFUP_TRANSPORT_MODE_SEUL_ELECTRIQUE = 20;
const AFUP_TRANSPORT_MODE_SEUL_HYBRIDE = 30;
const AFUP_TRANSPORT_MODE_PASSAGERS_THERMIQUE = 40;
const AFUP_TRANSPORT_MODE_PASSAGERS_ELECTRIQUE = 50;
const AFUP_TRANSPORT_MODE_PASSAGERS_HYBRIDE = 60;
const AFUP_TRANSPORT_MODE_BUS = 70;
const AFUP_TRANSPORT_MODE_TRAIN = 80;
const AFUP_TRANSPORT_MODE_AVION_ECO = 90;
const AFUP_TRANSPORT_MODE_AVION_BUSINESS = 100;
const AFUP_TRANSPORT_MODE_COMMUN = 110;

$AFUP_Tarifs_Forum = [
    Ticket::TYPE_INVITATION => 0,
    Ticket::TYPE_ORGANIZATION => 0,
    Ticket::TYPE_SPONSOR => 0,
    Ticket::TYPE_PRESS => 0,
    Ticket::TYPE_SPEAKER => 0,
    Ticket::TYPE_PROJECT => 0,
    Ticket::TYPE_TEACHER => 0,
    Ticket::TYPE_DAY_1 => 150,
    Ticket::TYPE_DAY_2 => 150,
    Ticket::TYPE_2_DAYS => 250,
    Ticket::TYPE_2_DAYS_AFUP => 150,
    Ticket::TYPE_DAY_1_AFUP => 100,
    Ticket::TYPE_DAY_2_AFUP => 100,
    Ticket::TYPE_2_DAYS_STUDENT => 150,
    Ticket::TYPE_DAY_1_STUDENT => 100,
    Ticket::TYPE_DAY_2_STUDENT => 100,
    Ticket::TYPE_2_DAYS_EARLY => 150,
    Ticket::TYPE_2_DAYS_AFUP_EARLY => 150,
    Ticket::TYPE_EARLY_PLUS_MEMBERSHIP => 150,
    Ticket::TYPE_DAY_1_STUDENT_EARLY => 100,
    Ticket::TYPE_DAY_2_STUDENT_EARLY => 100,
    Ticket::TYPE_2_DAYS_STUDENT_EARLY => 150,
    Ticket::TYPE_2_DAYS_VOUCHER => 200,
    Ticket::TYPE_2_DAYS_SPONSOR => 200,
    Ticket::TYPE_SPECIAL_PRICE => 0,
];

$GLOBALS['AFUP_Tarifs_Forum'] = $AFUP_Tarifs_Forum;

$AFUP_Tarifs_Forum_Lib = [
    Ticket::TYPE_INVITATION => 'Invitation',
    Ticket::TYPE_ORGANIZATION => 'Organisation',
    Ticket::TYPE_PROJECT => 'Projet PHP',
    Ticket::TYPE_SPONSOR => 'Sponsor',
    Ticket::TYPE_PRESS => 'Presse',
    Ticket::TYPE_TEACHER => 'Enseignement supérieur',
    Ticket::TYPE_SPEAKER => 'Conferencier',
    Ticket::TYPE_DAY_1 => 'Jour 1 ',
    Ticket::TYPE_DAY_2 => 'Jour 2',
    Ticket::TYPE_2_DAYS => '2 Jours',
    Ticket::TYPE_2_DAYS_AFUP => '2 Jours AFUP',
    Ticket::TYPE_DAY_1_AFUP => 'Jour 1 AFUP',
    Ticket::TYPE_DAY_2_AFUP => 'Jour 2 AFUP',
    Ticket::TYPE_2_DAYS_STUDENT => '2 Jours Etudiant',
    Ticket::TYPE_DAY_1_STUDENT => 'Jour 1 Etudiant',
    Ticket::TYPE_DAY_2_STUDENT => 'Jour 2 Etudiant',
    Ticket::TYPE_2_DAYS_EARLY => '2 Jours prévente',
    Ticket::TYPE_2_DAYS_AFUP_EARLY => '2 Jours AFUP prévente',
    Ticket::TYPE_EARLY_PLUS_MEMBERSHIP => '2 Jours prévente + adhésion',
    Ticket::TYPE_2_DAYS_STUDENT_EARLY => '2 Jours Etudiant prévente',
    Ticket::TYPE_2_DAYS_VOUCHER => '2 Jours avec coupon de réduction',
    Ticket::TYPE_2_DAYS_SPONSOR => '2 Jours par Sponsor',
    Ticket::TYPE_DAY_1_STUDENT_EARLY => '',
    Ticket::TYPE_DAY_2_STUDENT_EARLY => '',
    Ticket::TYPE_SPECIAL_PRICE => 'Tarif Spécial',
];

$GLOBALS['AFUP_Tarifs_Forum_Lib'] = $AFUP_Tarifs_Forum_Lib;
