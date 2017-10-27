<?php

use Afup\Site\Utils\Mail;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

if (
    !isset($_GET['ref'])
    ||
    !(
        preg_match('`ins-([0-9]+)`', $_GET['ref'], $matches)
        ||
        preg_match('`elephpant-([0-9]+)`', $_GET['ref'], $matches)
    )
) {
    die('Missing ref');
}

if (isset($_GET['prix'])) {
    $prix = abs(intval($_GET['prix']));
} else {
    $prix = 25;
}


$mail = new Mail();
$mail->sendSimpleMessage('Lien paiement stand AFUP', 'https://afup.org/pages/event-payment/index.php?prix=' . $prix . '&ref=' . $_GET['ref'] .  '&forum=' . $_GET['forum'], [['email' => $_GET['email'], 'name' => $_GET['email']]]);

echo 'mail envoyé sur ' . $_GET['email'];
