<?php

declare(strict_types=1);
require_once __DIR__ . '/../../../sources/Afup/Bootstrap/Http.php';

$paybox  = "<p>Il y a eu une erreur lors de votre paiement. Désolé.</p>";
$paybox .= "<p>Une questions ? N'hésitez pas à contacter <a href=\"mailto:tresorier@afup.org\">le trésorier</a>.</p>";
$paybox .= "<p><strong></srong><a href=\"index.php\">retour à votre compte</a></strong></p>";

$smarty->assign('paybox', $paybox);
$smarty->display('paybox.html');
