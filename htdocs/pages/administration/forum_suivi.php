<?php

// Impossible to access the file itself
use Afup\Site\Forum\Inscriptions;
use Afup\Site\Forum\Forum;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}





$forum = new Forum($bdd);
$forum_inscriptions = new Inscriptions($bdd);

if (!isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0) {
	$_GET['id_forum'] = $forum->obtenirDernier();
}
$smarty->assign('id_forum', $_GET['id_forum']);
$smarty->assign('forum_avenir', $forum->obtenir((int) $_GET['id_forum']));
$id_precedent = $forum->obtenirPrecedent((int) $_GET['id_forum']);
$smarty->assign('forum_precedent', $forum->obtenir($id_precedent));
$smarty->assign('forums', $forum->obtenirListe());

$suiviBrut = $forum_inscriptions->obtenirSuivi($_GET['id_forum']);
$smarty->assign('suivis', $suiviBrut);

$n = $n_1 = array();
if ($suiviBrut != false) {
    foreach ($suiviBrut as $s) {
        $n[] = $s['n'];
        $n_1[] = $s['n_1'];
    }
}

$smarty->assign('n', implode(', ' , $n));
$smarty->assign('n_1', implode(', ' , $n_1));
