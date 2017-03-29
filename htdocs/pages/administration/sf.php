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

$kernel = new \Afup\Site\Utils\SymfonyKernel();
$request = $kernel->getRequest('/admin/vote/' . $forumCFP['path']);
$response = $kernel->getResponse();

$smarty->assign('sfContent', $response->getContent());
$kernel->getKernel()->terminate($request, $response);