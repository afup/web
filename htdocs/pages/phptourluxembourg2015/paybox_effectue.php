<?php
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';
require_once dirname(__FILE__) . '/../../../sources/Afup/Bootstrap/_Common.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Inscriptions_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Facturation_Forum.php';
require_once dirname(__FILE__) . '/../../../sources/Afup/AFUP_Mail.php';

$forum_inscriptions = new AFUP_Inscriptions_Forum($bdd);
$forum_facturation = new AFUP_Facturation_Forum($bdd);

$forum_inscriptions->modifierEtatInscription($_GET['cmd'], AFUP_FORUM_ETAT_REGLE);
$forum_facturation->enregistrerInformationsTransaction($_GET['cmd'], $_GET['autorisation'], $_GET['transaction']);
if ($forum_facturation->estFacture($_GET['cmd'])) {
    $facture = $forum_facturation->obtenir($_GET['cmd']);

    // Send the invoice
    $forum_facturation->envoyerFacture($_GET['cmd']);

    // Send register confirmation
    $mail = new AFUP_Mail();
    $receiver = array(
        'email' => $facture['email'],
        'name'  => sprintf('%s %s', $facture['prenom'], $facture['nom']),
    );
    $data = $facture;

    if (!$mail->send('confirmation-inscription', $receiver, $data)) {
        $message = <<<HTML
Impossible d'envoyer la confirmation d'inscription après paiement pour le forum en cours.<br>
Facture : {$facture['reference']}<br/>
Contact : {$facture['prenom']} {$facture['nom']} &lt;{$facture['email']}&gt;
HTML;
        $mail->sendSimpleMessage(
            "Impossible d'envoyer la confirmation",
            $message,
            array(
                array(
                    'name' => 'Trésorier AFUP',
                    'email' => 'tresocier@afup.org',
                ),
                array(
                    'name' => 'Communication AFUP',
                    'email' => 'communication@afup.org',
                ),
            )
        );
    }

} else {
    // Send error to default
    // @TODO check if this happens or not
    $mail = new AFUP_Mail();
    $mail->sendSimpleMessage("Impossible d'envoyer la facture", 'Impossible de facturer la commande ' . htmlspecialchars($_GET['cmd']) . ' après paiement inscription forum.');
}

$smarty->display('paybox_effectue.html');
