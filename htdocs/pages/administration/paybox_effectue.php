<?php

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

$message  = "<p>Votre paiement a été enregistré. Merci et à bientôt.</p>";
$message .= "<p>Une questions ? N'hésitez pas à contacter <a href=\"mailto:tresorier@afup.org\">le trésorier</a>.</p>";
$message .= "<p><strong></srong><a href=\"index.php\">retour à votre compte</a></strong></p>";

$smarty->assign('paybox', $message);
$smarty->display('paybox.html');
