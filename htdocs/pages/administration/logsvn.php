<?php
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Oeuvres.php';
$logsSVN = new AFUP_Oeuvres($bdd);
$refresh = false;
if (isset($_GET["refresh"]) &&  $_GET["refresh"] == "true") $refresh = true;

$smarty->assign('logsvn'        , $logsSVN->extraireLogSVNBrut($refresh));


?>