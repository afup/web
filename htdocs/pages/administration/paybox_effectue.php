<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../sources/Afup/Bootstrap/Http.php';

$message  = "<p>Votre paiement a été enregistré. Merci et à bientôt.</p>";
$message .= "<p>Une questions ? N'hésitez pas à contacter <a href=\"mailto:tresorier@afup.org\">le trésorier</a>.</p>";
$message .= "<p>Je veux partager la nouvelle de mon adhésion à l'AFUP:</p>";
$message .= "<p><a href=\"https://twitter.com/share?ref_src=twsrc%5Etfw\" class=\"twitter-share-button\" data-text=\"Quelle bonne journée ! Je viens de régler ma cotisation à l’AFUP : une année de soutien à la communauté et d’avantages exclusifs autour de PHP !\" data-lang=\"fr\" data-show-count=\"false\" data-url=\"https://afup.org\">Tweet</a></p>";
$message .= "<script async src=\"https://platform.twitter.com/widgets.js\" charset=\"utf-8\"></script>";
$message .= "<p><strong><a href=\"/member\">retour à votre compte</a></strong></p>";

$smarty->assign('paybox', $message);
$smarty->display('paybox.html');
