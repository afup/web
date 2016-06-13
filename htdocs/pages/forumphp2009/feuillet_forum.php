<?php
use Afup\Site\Forum\Forum;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';
require_once dirname(__FILE__) . '/_config.inc.php';

setlocale(LC_TIME, 'fr_FR');
define("DS", DIRECTORY_SEPARATOR);




$oAfup = new Forum($bdd);
$sTable = $oAfup->genAgenda($config_forum['annee']);
$smarty->assign('agenda', $sTable);
$smarty->display('feuillet_forum.html');
