<?php
require_once '../../include/prepend.inc.php';

$paybox  = "<p>Votre paiement a été annulé. Désolé.</p>";
$paybox .= "<p>Une questions ? N'hésitez pas à contacter <a href=\"mailto:tresorier@afup.org\">le trésorier</a>.</p>";
$paybox .= "<p><strong></srong><a href=\"index.php\">retour à votre compte</a></strong></p>";

$smarty->assign('paybox', $paybox);
$smarty->display('paybox.html');
?>