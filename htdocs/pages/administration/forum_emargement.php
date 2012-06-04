<?php

$action = verifierAction(array('lister', 'lister_conferencier_orga'));
$tris_valides = array('i.date', 'i.nom', 'f.societe', 'i.etat');
$sens_valides = array( 'desc','asc' );
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Inscriptions_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Facturation_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';

$forum = new AFUP_Forum($bdd);
$forum_inscriptions = new AFUP_Inscriptions_Forum($bdd);
$forum_facturation = new AFUP_Facturation_Forum($bdd);

$list_champs = 'i.id, i.date, i.nom, i.prenom, i.email, f.societe, i.etat, i.coupon, i.type_inscription';
$list_ordre = 'I.nom asc';
$list_sens = 'desc';
$list_associatif = false;
$list_filtre = false;

if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides) && isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
	$list_ordre = $_GET['tri'] . ' ' . $_GET['sens'];
}

if (!isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0) {
	$_GET['id_forum'] = $forum->obtenirDernier();
}
$smarty->assign('id_forum', $_GET['id_forum']);

$smarty->assign('forum_tarifs_lib', $AFUP_Tarifs_Forum_Lib);

$smarty->assign('forums', $forum->obtenirListe());
if ($action == 'lister') {
    $smarty->assign('inscriptions', $forum_inscriptions->obtenirListePourEmargement($_GET['id_forum']));
} else {
    $smarty->assign('inscriptions', $forum_inscriptions->obtenirListePourEmargementConferencierOrga($_GET['id_forum']));
}
