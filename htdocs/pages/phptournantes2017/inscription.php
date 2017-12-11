<?php

if (isset($_GET['nolayout']) === false) {
    header('HTTP/1.0 301 Moved Permanently');
    header('Location:http://event.afup.org/phptournantes2017/tickets-inscriptions/');
}

use Afup\Site\Forum\Facturation;
use Afup\Site\Forum\Forum;
use Afup\Site\Forum\Inscriptions;
use Afup\Site\Utils\Pays;

require_once __DIR__ . '/../../include/prepend.inc.php';
require_once __DIR__ . '/_config.inc.php';
$smarty->display('inscriptions_fermes.html');
