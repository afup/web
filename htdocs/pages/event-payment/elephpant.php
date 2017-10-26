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


$mail = new Mail();
$mail->sendSimpleMessage('Lien paiement elephpant', 'https://afup.org/event-payment/index.php?ref=' . $_GET['ref'] .  '&forum=' . $_GET['forum'], ['email' => $_GET['email'], 'name' => $_GET['email']]);

echo 'mail envoyé sur ' . $_GET['email'];
