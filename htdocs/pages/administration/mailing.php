<?php

$action = verifierAction(array('index','mailing', 'ajouter', 'modifier', 'supprimer'));
$smarty->assign('action', $action);
set_time_limit(0);
require_once AFUP_'afup/AFUP_Mailing.php';

$mailing = new AFUP_Mailing($bdd);

if ($action == 'mailing')
{
    $formulaire = &instancierFormulaire();
    $formulaire->setDefaults(array('from_email'            => $GLOBALS['conf']->obtenir('mails|email_expediteur'),
                     'from_name' =>$GLOBALS['conf']->obtenir('mails|nom_expediteur'),
                     'subject' => 'Forum PHP 2009',
                     'body'    => 'poum',
                     'tos'      => 'xgorse@elao.com;'));
    $formulaire->addElement('header', null          , 'Mailling');
    $formulaire->addElement('text'  , 'from_name'         , 'Nom From'            , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'  , 'from_email'         , 'Email From'            , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'  , 'subject'         , 'Subject'            , array('size' => 50, 'maxlength' => 50));
    $formulaire->addElement('textarea', 'body', 'Body'     , array('cols' => 40, 'rows' => 15));
    $formulaire->addElement('textarea', 'tos', 'To'     , array('cols' => 40, 'rows' => 15));
    $formulaire->addElement('header', 'boutons'  , '');
    $formulaire->addElement('submit', 'soumettre', 'Soumettre');

    $formulaire->addRule('subject'      , 'subject manquant'             , 'required');
    $formulaire->addRule('body'      , 'body manquant'             , 'required');
    $formulaire->addRule('tos'      , 'to manquant'             , 'required');
    $formulaire->addRule('from_name'    , 'from manquant'           , 'required');
    $formulaire->addRule('from_email'    , 'from manquant'           , 'required');
    $formulaire->addRule('from_email'    , 'from invalide'           , 'email');
      if ($formulaire->validate())
      {
      $valeurs = $formulaire->exportValues();
      $email_tos = split(';',$valeurs['tos']);
     // var_dump($email_tos);die;
      $nb = 0;
      foreach ($email_tos as $nb =>$email_to)
      {
      	
        $mailing->envoyeMail(array($valeurs['from_email'],$valeurs['from_name']),$email_to, $valeurs['subject'],$valeurs['body'],true);
      	$nb++ ;
        if ($nb%200 == 0)
      	 {
      	   sleep(5);
      	 }
      }
        AFUP_Logs::log('Envoie mailing ' .$valeurs['subject']);
        afficherMessage('Le mail ', 'index.php?page=mailing');

      }
    $smarty->assign('formulaire', genererFormulaire($formulaire));
}


else
{

}

?>