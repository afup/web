<?php

// Impossible to access the file itself
use Afup\Site\Oeuvres;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$logsSVN = new Oeuvres($bdd);
$refresh = false;
if (isset($_GET["refresh"]) &&  $_GET["refresh"] == "true") $refresh = true;

$smarty->assign('loggit'        , $logsSVN->extraireLogGitBrut($refresh));
