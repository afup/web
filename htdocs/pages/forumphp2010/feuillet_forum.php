<?php
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';

setlocale(LC_TIME, 'fr_FR');
define("DS", DIRECTORY_SEPARATOR);


require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';

$oAfup = new AFUP_Forum($bdd);
$sTable = $oAfup->genAgenda($config_forum['annee']);
$smarty->assign('agenda', $sTable);
$smarty->display('feuillet_forum.html');
