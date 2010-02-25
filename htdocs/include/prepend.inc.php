<?php

// Initialisation
ob_start();
session_start();
define('AFUP_CHEMIN_RACINE', realpath(dirname(__FILE__) . '/../') . '/');
require_once AFUP_CHEMIN_RACINE . 'include/fonctions.inc.php';

// Configuration
require_once AFUP_CHEMIN_RACINE . 'classes/afup/AFUP_Configuration.php';
$conf = new AFUP_Configuration(AFUP_CHEMIN_RACINE . 'include/configuration.inc.php');
error_reporting($conf->obtenir('divers|niveau_erreur'));
ini_set('display_errors', $conf->obtenir('divers|afficher_erreurs'));
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . AFUP_CHEMIN_RACINE . 'classes/pear/');
header('Content-type: text/html; charset=UTF-8');

// On détermine sur quel sous-site on est
$serveur   = "";
$url = $_SERVER['REQUEST_URI'];
if (strrpos($url, '?') !== false) {
	$position = strrpos($url, '?');
	$url      = substr($url, 0, $position);
}
$position  = strrpos($url, '/');
$url       = substr($_SERVER['REQUEST_URI'], 0, $position);
$parties   = explode('/', $url);
$sous_site = array_pop($parties);
if (empty($sous_site) and strpos($_SERVER['HTTP_HOST'], "planete") !== false) {
	$sous_site = "planete";
	$serveur = "http://afup.org";
}

// Initialisation de Smarty
require_once AFUP_CHEMIN_RACINE . 'classes/smarty/Smarty.class.php';
$smarty = new Smarty;
$smarty->template_dir  = array(AFUP_CHEMIN_RACINE . 'templates/' . $sous_site . '/',
                               AFUP_CHEMIN_RACINE . 'templates/commun/');
$smarty->compile_dir   = AFUP_CHEMIN_RACINE . 'cache/templates';
$smarty->compile_id    = $sous_site;
$smarty->use_sub_dirs  = true;
$smarty->check_compile = true;
$smarty->php_handling  = SMARTY_PHP_ALLOW;
$smarty->assign('url_base', 'http://' . $_SERVER['HTTP_HOST'] . '/');
$smarty->assign('chemin_template', $serveur.$conf->obtenir('web|path').'/templates/' . $sous_site . '/');
$smarty->assign('chemin_javascript', $serveur.$conf->obtenir('web|path').'/javascript/');

// Initialisation de la couche d'abstraction de la base de données
require_once AFUP_CHEMIN_RACINE . 'classes/afup/AFUP_Base_De_Donnees.php';
$bdd = new AFUP_Base_De_Donnees($conf->obtenir('bdd|hote'),
                                $conf->obtenir('bdd|base'),
                                $conf->obtenir('bdd|utilisateur'),
                                $conf->obtenir('bdd|mot_de_passe'));
$bdd->executer("SET NAMES 'utf8'");