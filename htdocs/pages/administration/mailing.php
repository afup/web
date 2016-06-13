<?php

// Impossible to access the file itself
use Afup\Site\Forum\Inscriptions;
use Afup\Site\Forum\Forum;
use Afup\Site\Forum\AppelConferencier;
use Afup\Site\Association\Assemblee_Generale;
use Afup\Site\BlackList;
use Afup\Site\Utils\Mailing;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('index','mailing', 'ajouter', 'modifier', 'supprimer'));
$smarty->assign('action', $action);
set_time_limit(0);

require_once 'phpmailer/class.phpmailer.php';

$forum = new Forum($bdd);
$blackList = new BlackList($bdd);
$mailing = new Mailing($bdd);

if ($action == 'mailing')
{
    switch ($_GET['liste']) {
        case 'membre_a_jour_cotisation':
            $assemblee = new Assemblee_Generale($bdd);
            $liste = $assemblee->obtenirListeEmailPersonnesAJourDeCotisation();
            break;
        case 'ancien_conferencier':
            $forum_appel = new AppelConferencier($bdd);
            $liste = $forum_appel->obtenirListeEmailAncienConferencier();
            break;
        case 'ancien_visiteur':
            $inscriptions = new Inscriptions($bdd);
            $liste = $inscriptions->obtenirListeEmailAncienVisiteurs();
            break;
        default:
            $liste = '';
            break;
    }
    $formulaire = &instancierFormulaire();
    $id_forum = $forum->obtenirDernier();
    $rs_forum = $forum->obtenir($id_forum);
    $formulaire->setDefaults(array('from_email' => $GLOBALS['conf']->obtenir('mails|email_expediteur'),
                                   'from_name'  => $GLOBALS['conf']->obtenir('mails|nom_expediteur'),
                                   'subject'    => $rs_forum['titre'],
                                   'body'       => '',
                                   'tos'        => $liste));

    $formulaire->addElement('header'  , null        , 'Mailling');
    $formulaire->addElement('text'    , 'from_name' , 'Expéditeur   ', array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'from_email', 'Email'        , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'subject'   , 'Sujet'        , array('size' => 50, 'maxlength' => 50));
    $formulaire->addElement('textarea', 'body'      , 'Texte'        , array('cols' => 60, 'rows' => 20));
    $formulaire->addElement('static'  , 'note'      , ''          , 'Listes des adresses mails séparées par des points-virgules');
    $formulaire->addElement('textarea', 'tos'       , 'Destinataires', array('cols' => 60, 'rows' => 15));
    $formulaire->addElement('header'  , 'boutons'   , '');
    $formulaire->addElement('submit'  , 'soumettre' , 'Soumettre');

    $formulaire->addRule('subject'   , 'subject manquant', 'required');
    $formulaire->addRule('body'      , 'body manquant'   , 'required');
    $formulaire->addRule('tos'       , 'to manquant'     , 'required');
    $formulaire->addRule('from_name' , 'from manquant'   , 'required');
    $formulaire->addRule('from_email', 'from manquant'   , 'required');
    $formulaire->addRule('from_email', 'from invalide'   , 'email');

    if ($formulaire->validate()) {
        $valeurs = $formulaire->exportValues();
        $email_tos = explode(';',$valeurs['tos']);
        $nb = 0;
        $liste = $blackList->obtenirListe();
        foreach ($email_tos as $nb =>$email_to) {
            $email_to = trim($email_to);
            if ((filter_var($email_to, FILTER_VALIDATE_EMAIL))) {
                if (!(in_array($email_to, $liste))) {
                    Mailing::envoyerMail($valeurs['from_email'], $email_to, $valeurs['subject'], $body);
                    if (((++$nb) % 200) == 0) {
                        sleep(5);
                    }
                }
            }
        }
        Logs::log('Envoi mailing ' .$valeurs['subject']);
        afficherMessage('Le mail a été envoyé', 'index.php?page=mailing');
    }
    $smarty->assign('formulaire', genererFormulaire($formulaire));
}
