<?php

require_once 'Afup/AFUP_Inscriptions_Forum.php';
require_once 'Afup/AFUP_Facturation_Forum.php';
require_once 'Afup/AFUP_Forum.php';

$forum = new AFUP_Forum($bdd);
$forum_inscriptions = new AFUP_Inscriptions_Forum($bdd);

if (!isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0) {
	$_GET['id_forum'] = $forum->obtenirDernier();
}
$smarty->assign('id_forum', $_GET['id_forum']);
$smarty->assign('forums', $forum->obtenirListe());

$smarty->assign('suivis', $forum_inscriptions->obtenirSuivi($_GET['id_forum']));
