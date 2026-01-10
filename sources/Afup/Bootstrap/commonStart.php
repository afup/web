<?php

declare(strict_types=1);

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
