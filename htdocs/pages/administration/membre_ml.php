<?php

// Impossible to access the file itself
use Afup\Site\Utils\Base_De_Donnees;
use Afup\Site\Utils\Sympa;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}



$sympaBdd = new Base_De_Donnees(
    $conf->obtenir('sympa|hote'),
    $conf->obtenir('sympa|base'),
    $conf->obtenir('sympa|utilisateur'),
    $conf->obtenir('sympa|mot_de_passe')
);

$sympa = new Sympa($sympaBdd, $conf->obtenir('sympa|config_url'), $conf->obtenir('sympa|host'));
$listes = $sympa->getAllMailingList();

if ($_POST) {
    if (isset($_POST['action']) && $_POST['action'] == 'inscription') {
        if (isset($_POST['ml']) && isset($listes[$_POST['ml']]) && $listes[$_POST['ml']]['unsubscribe'] == 'auth') {
            $sympa->subscribe($droits->obtenirEmail(), $_POST['ml'], $droits->obtenirNomComplet());
        }
    }
    if (isset($_POST['action']) && $_POST['action'] == 'desinscription') {
        if (isset($_POST['ml']) && isset($listes[$_POST['ml']]) && $listes[$_POST['ml']]['unsubscribe'] == 'auth') {
            $sympa->unsubscribe($droits->obtenirEmail(), $_POST['ml']);
        }
    }
}

$mes_listes = $sympa->getMailingListUser($droits->obtenirEmail());

$smarty->assign('ml_afup', $listes);
$smarty->assign('mes_ml_afup', $mes_listes);
