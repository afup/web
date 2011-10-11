<?php

$action = verifierAction(array('index','mailing', 'ajouter', 'modifier', 'supprimer'));
$smarty->assign('action', $action);
set_time_limit(0);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Mailing.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';
require_once 'phpmailer/class.phpmailer.php';

$forum = new AFUP_Forum($bdd);

$mailing = new AFUP_Mailing($bdd);

if ($action == 'mailing')
{
    $formulaire = &instancierFormulaire();
    $id_forum = $forum->obtenirDernier();
    $rs_forum = $forum->obtenir($id_forum);
    $formulaire->setDefaults(array('from_email' => $GLOBALS['conf']->obtenir('mails|email_expediteur'),
                                   'from_name'  => $GLOBALS['conf']->obtenir('mails|nom_expediteur'),
                                   'subject'    => $rs_forum['titre'],
                                   'body'       => '',
                                   'tos'        => 'Listes des adresses mails séparées par des points-virgules'));

    $formulaire->addElement('header'  , null        , 'Mailling');
    $formulaire->addElement('text'    , 'from_name' , 'Nom From'  , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'from_email', 'Email From', array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'subject'   , 'Subject'   , array('size' => 50, 'maxlength' => 50));
    $formulaire->addElement('textarea', 'body'      , 'Body'      , array('cols' => 60, 'rows' => 20));
    $formulaire->addElement('textarea', 'tos'       , 'To'        , array('cols' => 60, 'rows' => 15));
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
        foreach ($email_tos as $nb =>$email_to) {
            $mail = new PHPMailer;
            $mail->AddAddress($email_to);
            $mail->From = $valeurs['from_email'];
            $mail->FromName = $valeurs['from_name'];
            $mail->Subject = $valeurs['subject'];
            $mail->Body = $valeurs['body'];
            $mail->Send();
            echo 'envoi mail<br/>';
            if (((++$nb) % 200) == 0) {
                sleep(5);
                echo 'pause<br/>';
            }
        }
        AFUP_Logs::log('Envoi mailing ' .$valeurs['subject']);
        die();
        afficherMessage('Le mail a été envoyé', 'index.php?page=mailing');
    }
    $smarty->assign('formulaire', genererFormulaire($formulaire));
} else {

}

?>