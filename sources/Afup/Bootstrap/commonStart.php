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
