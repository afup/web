<?php

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


mail($_GET['email'], 'Lien paiement elephpant', 'https://afup.org/event-payment/index.php?ref=' . $_GET['ref'] .  '&forum=' . $_GET['forum']);

echo 'mail envoyé';
