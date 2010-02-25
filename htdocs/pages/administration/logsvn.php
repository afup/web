<?php
require_once AFUP_CHEMIN_RACINE . 'classes/afup/AFUP_Oeuvres.php';
$logsSVN = new AFUP_Oeuvres($bdd);
$refresh = false;
if (isset($_GET["refresh"]) &&  $_GET["refresh"] == "true") $refresh = true;

$smarty->assign('logsvn'        , $logsSVN->extraireLogSVNBrut($refresh));


?>