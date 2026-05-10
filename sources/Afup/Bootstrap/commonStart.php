<?php

declare(strict_types=1);

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
