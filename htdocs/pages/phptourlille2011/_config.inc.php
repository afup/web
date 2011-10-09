<?php
/**
    TODO en plus de cette config :
    Créer la ligne afup_forum et mettre l'id de la ligne dans $config_forum['id']
    Il faut aussi verifier le contenu des template (rechercher la date de l'année précedente )
    Modifer le pdf du formulaire papier dans "/site/templates/forumphpXXXX/inscription-forum.pdf"
       à partir du doc dans "/sources/doc/inscription au forum.odt"
    Modifer le pdf du dossier sponsors dans "/site/templates/forumphp2009/pdf/Forum-PHP-2009-dossier-sponsor.pdf"
       à partir du doc dans "/sources/forum/2009/Forum-PHP-2009-dossier-sponsor.odt"
 */
// Param de configuration sur site du Forum PHP

define('AFUP_CHEMIN_SOURCE', realpath(dirname(__FILE__) . '/../../classes/afup/'));
date_default_timezone_set("Europe/Paris");
ini_set('display_errors',  $conf->obtenir('divers|afficher_erreurs'));

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum_Coupon.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum_Partenaires.php';
$forums = new AFUP_Forum($bdd);
$coupons = new AFUP_Forum_Coupon($bdd);
$partenairesForum = new AFUP_Forum_Partenaires($bdd);

$idForum = 6; // 6 = PHPTour
$config_forum = $forums->obtenir($idForum);
$config_forum['date_debut'] = strtotime($config_forum['date_debut']);
$config_forum['date_fin'] = strtotime($config_forum['date_fin']);
$detailsCoupon = array_values($coupons->obtenirCouponsForum($idForum));
$config_forum['coupons'] = array_merge($detailsCoupon, array_map("strtolower",$detailsCoupon));

$config_forum['project_ids'] = array();

$smarty->assign('forum_annee', $config_forum['annee']);

$partenaires = $partenairesForum->obtenirTousPartenairesForum($idForum);
$smarty->assign('partenaires', $partenaires);
