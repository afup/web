<?php

// Inclusion de l'autoload de composer
require_once dirname(__FILE__) . '/../../../vendor/autoload.php';

// Configuration du composant de traduction
$lang = 'fr';
$langs = ['fr', 'en'];
if (isset($_GET['lang']) && in_array($_GET['lang'], $langs)) {
    $lang = $_GET['lang'];
}
$translator = new \Symfony\Component\Translation\Translator($lang);
$translator->addLoader('xliff', new \Symfony\Component\Translation\Loader\XliffFileLoader());
$translator->addResource('xliff', dirname(__FILE__) . '/../../../translations/inscription.en.xlf', 'en');
$translator->addResource('xliff', dirname(__FILE__) . '/../../../translations/cfp.en.xlf', 'en');
$translator->setFallbackLocales(array('fr'));
$smarty->register_modifier('trans', [$translator, 'trans']);


$debug = false;
if ($_SERVER['HTTP_HOST'] === 'afup.dev') {
    $debug = true;
}

// Configuration de twig
$loader = new Twig_Loader_Filesystem(dirname(__FILE__) . '/../../../htdocs/templates/');
$twig = new Twig_Environment($loader, array(
    'cache' => dirname(__FILE__) . '/../../../htdocs/tmp/twig/',
    'debug' => $debug
));
$twig->addGlobal('url_base', $smarty->get_template_vars('url_base'));
$twig->addGlobal('chemin_template', $smarty->get_template_vars('chemin_template'));
$twig->addGlobal('chemin_javascript', $smarty->get_template_vars('chemin_javascript'));;