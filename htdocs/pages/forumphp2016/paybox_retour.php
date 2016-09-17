<?php
use Afup\Site\Forum\Inscriptions;
use Afup\Site\Forum\Facturation;
use Afup\Site\Utils\Mail;

require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';
require_once dirname(__FILE__) . '/../../../sources/Afup/Bootstrap/_Common.php';



$forum_inscriptions = new Inscriptions($bdd);
$forum_facturation = new Facturation($bdd);

$payboxIps = [
  '194.2.122.158',
  '194.2.122.190',
  '194.2.160.64',
  '194.2.160.66',
  '194.2.160.75',
  '194.2.160.80',
  '194.2.160.82',
  '194.2.160.91',
  '195.101.99.73',
  '195.101.99.76',
  '195.25.67.0',
  '195.25.67.11',
  '195.25.67.2',
  '195.25.67.22',
  '195.25.7.146',
  '195.25.7.157',
  '195.25.7.159',
  '195.25.7.166',
];
if (in_array($_SERVER['REMOTE_ADDR'], $payboxIps) === false) {
    /// Ici sont rencensees les IP indiquées par paybox dans leur doc
    die('...');
}


$status = $_GET['status'];
$etat = AFUP_FORUM_ETAT_ERREUR;

if ($status === '00000') {
    $etat = AFUP_FORUM_ETAT_REGLE;
} elseif ($status === '00015') {
    // Designe un paiement deja effectue : on a surement deja eu le retour donc on s'arrete
    die;
} elseif ($status === '00117') {
    $etat = AFUP_FORUM_ETAT_ANNULE;
} elseif (substr($status, 0, 3) === '001') {
    $etat = AFUP_FORUM_ETAT_REFUSE;
}

$forum_inscriptions->modifierEtatInscription($_GET['cmd'], $etat);
$forum_facturation->enregistrerInformationsTransaction($_GET['cmd'], $_GET['autorisation'], $_GET['transaction']);

if ($etat === AFUP_FORUM_ETAT_REGLE && $forum_facturation->estFacture($_GET['cmd'])) {
    $facture = $forum_facturation->obtenir($_GET['cmd']);

    // Send the invoice
    $forum_facturation->envoyerFacture($facture);

    // Send register confirmation
    $mail = new Mail();
    $registrations = $forum_inscriptions->getRegistrationsByReference($facture['reference']);

    foreach ($registrations as $registration) {
        $receiver = array(
            'email' => $registration['email'],
            'name'  => sprintf('%s %s', $registration['prenom'], $registration['nom']),
        );
        $data = $registration;

        if (!$mail->send('confirmation-inscription-forum-php-2016', $receiver, $data)) {
            $message = <<<HTML
Impossible d'envoyer la confirmation d'inscription après paiement pour le forum en cours.<br>
Facture : {$registration['reference']}<br/>
Contact : {$registration['prenom']} {$registration['nom']} &lt;{$registration['email']}&gt;
HTML;
            $mail->sendSimpleMessage(
                "Impossible d'envoyer la confirmation",
                $message,
                array(
                    array(
                        'name' => 'Trésorier AFUP',
                        'email' => 'tresorier@afup.org',
                    ),
                    array(
                        'name' => 'Communication AFUP',
                        'email' => 'communication@afup.org',
                    ),
                )
            );
        }
    }

}
