<?php

// Impossible to access the file itself
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Oeuvres.php';
$logsSVN = new AFUP_Oeuvres($bdd);
$refresh = false;
if (isset($_GET["refresh"]) &&  $_GET["refresh"] == "true") $refresh = true;

$smarty->assign('loggit'        , $logsSVN->extraireLogGitBrut($refresh));
