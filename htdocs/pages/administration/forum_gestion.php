<?php

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer', 'ajouter_coupon', 'supprimer_coupon'));
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum_Coupon.php';
$forums = new AFUP_Forum($bdd);
$coupons = new AFUP_Forum_Coupon($bdd);

if ($action == 'lister') {
    $evenements = $forums->obtenirListe(null, '*', 'date_debut desc');
    foreach ($evenements as &$e) {
        $e['coupons'] = $coupons->obtenirCouponsForum($e['id']);
    }
    $smarty->assign('evenements', $evenements);
} elseif ($action == 'ajouter_coupon') {
    if ($coupons->ajouter($_GET['id_forum'], $_GET['coupon'])) {
        AFUP_Logs::log('Ajout du coupon de forum');
        afficherMessage('Le coupon a été ajouté', 'index.php?page=forum_gestion&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de l\'ajout du coupon', 'index.php?page=forum_gestion&action=lister', true);
    }
} elseif ($action == 'supprimer_coupon') {
    if ($coupons->supprimer($_GET['id'])) {
        AFUP_Logs::log('Suppression du coupon de forum ' . $_GET['id']);
        afficherMessage('Le coupon a été supprimé', 'index.php?page=forum_gestion&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression du coupon', 'index.php?page=forum_gestion&action=lister', true);
    }
}