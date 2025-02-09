<?php

declare(strict_types=1);

use AppBundle\Controller\LegacyController;

// Impossible to access the file itself
/** @var LegacyController $this */
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

require_once __DIR__ . '/../../../sources/Afup/Bootstrap/Http.php';

$paybox  = "<p>Votre paiement a été refusé. Désolé.</p>";
$paybox .= "<p>Une questions ? N'hésitez pas à contacter <a href=\"mailto:tresorier@afup.org\">le trésorier</a>.</p>";
$paybox .= "<p><strong></srong><a href=\"index.php\">retour à votre compte</a></strong></p>";

$smarty->assign('paybox', $paybox);
$smarty->display('paybox.html');
