<?php

// Impossible to access the file itself
use Afup\Site\Utils\Web;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
	trigger_error("Direct access forbidden.", E_USER_ERROR);
	exit;
}


$web = new Web($bdd);
$update = false;
if (isset($_GET['update']) and $_GET['update'] == 'true') {
	$update = true;
    if ($web->mettreAJour($update)) {
		Logs::log('Mise à jour du site Web');
		afficherMessage('Le site Web a été mis à jour', 'index.php?page=updatesvn');
    }
}
