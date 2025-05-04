<?php

declare(strict_types=1);
use Afup\Site\Forum\Facturation;
use Afup\Site\Forum\Forum;
use Afup\Site\Forum\Inscriptions;
use Afup\Site\Forum\InscriptionType;

require_once __DIR__ . '/../../../sources/Afup/Bootstrap/Http.php';


$forum_inscriptions = new Inscriptions($bdd);
$forum_facturation = new Facturation($bdd);
$forumEvent = new Forum($bdd);
$incriptionType = new InscriptionType();

$query = parse_url((string) $_SERVER['HTTP_REFERER'], PHP_URL_QUERY); // Should be like http://event.afup.org/paiement-confirme?cmd=F201610-0707-CCMBE-68287
parse_str($query, $result); // Should contains cmd=XXXX

if (isset($cmd) === false) {
    die;
}

$inscriptions = $forum_inscriptions->getRegistrationsByReference($cmd);
$invoice = $forum_facturation->obtenir($cmd);
$event = $forumEvent->obtenir($invoice['id_forum']);

echo $twig->render('paiement/payment_tracking.html.twig', [
    'inscriptions' => $inscriptions,
    'invoice' => $invoice,
    'inscriptionType' => $incriptionType,
    'event' => $event,
]);
