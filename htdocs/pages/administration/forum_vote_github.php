<?php

// Impossible to access the file itself
use Afup\Site\Forum\Forum;
use Afup\Site\Forum\AppelConferencier;
use Afup\Site\Droits;
use Afup\Site\Utils\Pays;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$forum = new Forum($bdd);
$forum_appel = new AppelConferencier($bdd);
$droits = new Droits($bdd);

if (!isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0) {
    $_GET['id_forum'] = $forum->obtenirDernier();
}
$idForum = (int)$_GET['id_forum'];

$smarty->assign('id_forum', $_GET['id_forum']);
$smarty->assign('forums', $forum->obtenirListe());

$forumCFP = $forum->obtenir($idForum);

$kernel = new \Afup\Site\Utils\SymfonyKernel();
$request = $kernel->getRequest('/admin/vote/' . $forumCFP['path']);
$response = $kernel->getResponse();

$smarty->assign('sfContent', $response->getContent());
$kernel->getKernel()->terminate($request, $response);