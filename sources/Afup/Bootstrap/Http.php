<?php

declare(strict_types=1);
/**
 * Fichier (bootstrap) du contexte HTTP (page web)
 *
 * Ce fichier doit contenir l'ensemble des directives d'initialisation
 * nécessaire au chargement de toute l'application pour une exécution
 * d'une page web (script php à destination du web)
 *
 * Ce fichier est systématiquement à inclure en haut de chaque
 * page.
 *
 * @author    Perrick Penet   <perrick@noparking.fr>
 * @author    Olivier Hoareau <olivier@phppro.fr>
 * @copyright 2010 Association Française des Utilisateurs de PHP
 *
 * @category AFUP
 * @package  AFUP
 * @group    Bootstraps
 */

// chargement des paramétrages génériques / multi-contextuels de l'application

use Afup\Site\Corporate\Site;
use Smarty\Smarty;

require_once __DIR__ . '/_Common.php';

// initialisation de la session / requête
if (ob_get_level() === 0) {
    ob_start();
}

// mise à jour des paramétrages PHP en fonction de la configuration

if (getenv('SYMFONY_ENV') === 'prod') {
    ini_set('error_reporting',  (string) (E_ALL ^ E_WARNING ^ E_NOTICE));
    ini_set('display_errors', '0');
} else {
    ini_set('error_reporting', (string) E_ALL);
    ini_set('display_errors', '1');
}
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . __DIR__ . '/../../../dependencies/PEAR/');

header('Content-type: text/html; charset=UTF-8');

// choix du 'sous-site' en fonction de l'url

$serveur   = '';
$url = $_SERVER['REQUEST_URI'];
if (strrpos((string) $url, '?') !== false) {
    $position = strrpos((string) $url, '?');
    $url      = substr((string) $url, 0, $position);
}
$position  = strrpos((string) $url, '/');
$url       = substr((string) $_SERVER['REQUEST_URI'], 0, $position);
$parties   = explode('/', $url);
$sous_site = array_pop($parties);

// initialisation de Smarty, le moteur de template (html)

$smarty = new Smarty();
$smarty->setTemplateDir([
    AFUP_CHEMIN_RACINE . 'templates/' . $sous_site . '/',
    AFUP_CHEMIN_RACINE . 'templates/commun/',
]);
$smarty->setCompileDir(AFUP_CHEMIN_RACINE . 'cache/templates');
$smarty->compile_id    = $sous_site;
$smarty->use_sub_dirs  = true;
$smarty->compile_check = Smarty::COMPILECHECK_ON;
$smarty->registerPlugin("modifier","stripslashes", "stripslashes");
$smarty->registerPlugin("modifier","floatval", "floatval");

$smarty->assign('url_base',          'https://' . $_SERVER['HTTP_HOST'] . '/');
$smarty->assign('chemin_template',   $serveur . Site::WEB_PATH . 'templates/' . $sous_site . '/');
$smarty->assign('chemin_javascript', $serveur . Site::WEB_PATH . 'javascript/');

require_once(__DIR__ . '/commonStart.php');
