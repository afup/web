<?php

declare(strict_types=1);

use AppBundle\Event\Model\Ticket;
use CCMBenchmark\Ting\Services;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;

// Inclusion de l'autoload de composer
require_once __DIR__ . '/../../../vendor/autoload.php';

// Configuration du composant de traduction
$lang = 'fr';
$langs = ['fr', 'en'];
if (isset($_GET['lang']) && in_array($_GET['lang'], $langs)) {
    $lang = $_GET['lang'];
}
$translator = new Translator($lang);
$translator->addLoader('xliff', new XliffFileLoader());
$translator->addResource('xliff', __DIR__ . '/../../../translations/inscription.en.xlf', 'en');
$translator->addResource('xliff', __DIR__ . '/../../../translations/cfp.en.xlf', 'en');
$translator->setFallbackLocales(['fr']);
if (isset($smarty)) {
    $smarty->register_modifier('trans', $translator->trans(...));
}


$debug = false;
if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'afup.dev') {
    $debug = true;
}

define('AFUP_FORUM_REGLEMENT_CARTE_BANCAIRE', 0);
define('AFUP_FORUM_REGLEMENT_CHEQUE', 1);
define('AFUP_FORUM_REGLEMENT_VIREMENT', 2);
define('AFUP_FORUM_REGLEMENT_AUCUN', 3);
define('AFUP_FORUM_REGLEMENT_A_POSTERIORI', 4);

define('AFUP_COTISATION_PERSONNE_PHYSIQUE', 30);
define('AFUP_COTISATION_PERSONNE_MORALE', 150);
define('AFUP_PERSONNE_MORALE_SEUIL', 3);

define('AFUP_RAISON_SOCIALE', 'AFUP');
define('AFUP_ADRESSE', "32, Boulevard de Strasbourg\nCS 30108");
define('AFUP_CODE_POSTAL', '75468');
define('AFUP_VILLE', 'Paris Cedex 10');
define('AFUP_EMAIL', 'bureau@afup.org');
define('AFUP_SIRET', '500 869 011 00022');
define('AFUP_NUMERO_TVA', 'FR27 500 869 011');

// Ticket transport distance
define('AFUP_TRANSPORT_DISTANCE_0', 0);
define('AFUP_TRANSPORT_DISTANCE_25_50', 25);
define('AFUP_TRANSPORT_DISTANCE_50_100', 50);
define('AFUP_TRANSPORT_DISTANCE_100_500', 100);
define('AFUP_TRANSPORT_DISTANCE_500_1000', 500);
define('AFUP_TRANSPORT_DISTANCE_1000', 1000);

// Ticket transport mode
define('AFUP_TRANSPORT_MODE_SEUL_THERMIQUE', 10);
define('AFUP_TRANSPORT_MODE_SEUL_ELECTRIQUE', 20);
define('AFUP_TRANSPORT_MODE_SEUL_HYBRIDE', 30);
define('AFUP_TRANSPORT_MODE_PASSAGERS_THERMIQUE', 40);
define('AFUP_TRANSPORT_MODE_PASSAGERS_ELECTRIQUE', 50);
define('AFUP_TRANSPORT_MODE_PASSAGERS_HYBRIDE', 60);
define('AFUP_TRANSPORT_MODE_BUS', 70);
define('AFUP_TRANSPORT_MODE_TRAIN', 80);
define('AFUP_TRANSPORT_MODE_AVION_ECO', 90);
define('AFUP_TRANSPORT_MODE_AVION_BUSINESS', 100);
define('AFUP_TRANSPORT_MODE_COMMUN', 110);

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


// Initialisation de ting
$services = new Services();
$services->get('ConnectionPool')->setConfig([
    'main' => [
        'namespace' => '\CCMBenchmark\Ting\Driver\Mysqli',
        'master' => [
            'host'      => $GLOBALS['AFUP_CONF']->obtenir('database_host'),
            'user'      => $GLOBALS['AFUP_CONF']->obtenir('database_user'),
            'password'  => $GLOBALS['AFUP_CONF']->obtenir('database_password'),
            'port'      => 3306,
        ],
    ],
]);

$services
    ->get('MetadataRepository')
    ->batchLoadMetadata(
        'AppBundle\Event\Model\Repository',
        __DIR__ . '/../Event/Model/Repository/*.php',
        ['default' => ['database' => $GLOBALS['AFUP_CONF']->obtenir('database_name')]],
    )
;
$services->set('security.csrf.token_manager', fn(): CsrfTokenManager => new CsrfTokenManager());
