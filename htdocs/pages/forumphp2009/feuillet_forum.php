<?php
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';

setlocale(LC_TIME, 'fr_FR');
define("DS", DIRECTORY_SEPARATOR);


require_once(AFUP_CHEMIN_RACINE ."classes". DS ."afup". DS . "AFUP_Forum.php");

/**
 * Construction des tableaux
 */
$oAfup = new AFUP_Forum($bdd);
$sTable = $oAfup->genAgenda($config_forum['annee']);
$smarty->assign('agenda', $sTable);
$smarty->display('feuillet_forum.html');
