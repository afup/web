<?php

if (isset($_GET['nolayout']) === false) {
    header('HTTP/1.0 301 Moved Permanently');
    header('Location:http://event.afup.org/php-tour-2016/tickets-inscriptions/');
}

require_once __DIR__ . '/../../include/prepend.inc.php';
require_once __DIR__ . '/_config.inc.php';
$smarty->display('inscriptions_fermes.html');
