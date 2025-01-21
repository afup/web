<?php
use Afup\Site\Forum\Inscriptions;

require_once __DIR__ .'/../../../sources/Afup/Bootstrap/Http.php';


$forum_inscriptions = new Inscriptions($bdd);
$forum_facturation = new \Afup\Site\Forum\Facturation($bdd);
$forumEvent = new \Afup\Site\Forum\Forum($bdd);
$incriptionType = new \Afup\Site\Forum\InscriptionType();

$query = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY); // Should be like http://event.afup.org/paiement-confirme?cmd=F201610-0707-CCMBE-68287
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
    'event' => $event
]);
