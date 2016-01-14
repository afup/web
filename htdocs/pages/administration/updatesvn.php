<?php

// Impossible to access the file itself
if (!defined('PAGE_LOADED_USING_INDEX')) {
	trigger_error("Direct access forbidden.", E_USER_ERROR);
	exit;
}

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Web.php';
$web = new AFUP_Web($bdd);
$update = false;
if (isset($_GET['update']) and $_GET['update'] == 'true') {
	$update = true;
    if ($web->mettreAJour($update)) {
		AFUP_Logs::log('Mise à jour du site Web');
		afficherMessage('Le site Web a été mis à jour', 'index.php?page=updatesvn');
    }
}
