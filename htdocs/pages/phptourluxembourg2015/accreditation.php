<?php

require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Pays.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Accreditation_Presse.php';
$pays = new AFUP_Pays($bdd);
$presse = new AFUP_Accreditation_Presse($bdd);

// On créé le formulaire
$formulaire = &instancierFormulaire();
$formulaire->setDefaults(array('civilite' => 'M.',
                               'id_pays'  => 'FR'));

$formulaire->addElement('header'  , ''            , 'Demande d\'accr&eacute;ditation');
$formulaire->addElement('text'    , 'titre_revue' , 'Titre de la revue' , array('size' => 30, 'maxlength' => 100));
$formulaire->addElement('select'  , 'civilite'    , 'Civilité'       , array('M.' => 'M.', 'Mme' => 'Mme', 'Mlle' => 'Mlle'));
$formulaire->addElement('text'    , 'nom'         , 'Nom'            , array('size' => 30, 'maxlength' => 40));
$formulaire->addElement('text'    , 'prenom'      , 'Prénom'         , array('size' => 30, 'maxlength' => 40));
$formulaire->addElement('text'    , 'carte_presse', 'N° de carte de presse', array('size' => 30, 'maxlength' => 50));
$formulaire->addElement('textarea', 'adresse'     , 'Adresse'        , array('cols' => 42, 'rows' => 2));
$formulaire->addElement('text'    , 'code_postal' , 'Code postal'    , array('size' => 6, 'maxlength' => 10));
$formulaire->addElement('text'    , 'ville'       , 'Ville'          , array('size' => 30, 'maxlength' => 50));
$formulaire->addElement('select'  , 'id_pays'     , 'Pays'           , $pays->obtenirPays());
$formulaire->addElement('text'    , 'telephone'   , 'Téléphone'      , array('size' => 20, 'maxlength' => 20));
$formulaire->addElement('text'    , 'email'       , 'Email'          , array('size' => 30, 'maxlength' => 100));
$formulaire->addElement('textarea', 'commentaires', 'Commentaires'   , array('cols' => 42, 'rows' => 4));
$formulaire->addElement('submit'  , 'soumettre'   , 'Soumettre');

$formulaire->addRule('titre_revue' , 'Titre de la revue manquante' , 'required');
$formulaire->addRule('nom' , 'Nom manquant' , 'required');
$formulaire->addRule('prenom' , 'Prénom manquant' , 'required');
$formulaire->addRule('carte_presse' , 'Carte presse manquante' , 'required');
$formulaire->addRule('adresse' , 'Adresse manquante' , 'required');
$formulaire->addRule('code_postal' , 'Code postal manquant' , 'required');
$formulaire->addRule('ville' , 'Ville manquante' , 'required');
$formulaire->addRule('telephone' , 'Téléphone manquant' , 'required');
$formulaire->addRule('email' , 'Email manquant' , 'required');
$formulaire->addRule('email' , 'Email invalide' , 'email');

if ($formulaire->validate()) {
    $valeurs = $formulaire->exportValues();
    $ok = $presse->ajouter(null,
                           time(),
                           $formulaire->exportValue('titre_revue'),
                           $formulaire->exportValue('civilite'),
                           $formulaire->exportValue('nom'),
                           $formulaire->exportValue('prenom'),
                           $formulaire->exportValue('carte_presse'),
                           $formulaire->exportValue('adresse'),
                           $formulaire->exportValue('code_postal'),
                           $formulaire->exportValue('ville'),
                           $formulaire->exportValue('id_pays'),
                           $formulaire->exportValue('telephone'),
                           $formulaire->exportValue('email'),
                           $formulaire->exportValue('commentaires'),
                           6,  // 6 = PHPTour
                           1); // 1 = valide
    if ($ok) {
        require_once 'phpmailer/class.phpmailer.php';
        $mail = new PHPMailer;
        $mail->AddAddress('communication@afup.org', 'Communication AFUP');
        $mail->AddAddress('bureau@afup.org', 'Bureau AFUP');
        $mail->From = $formulaire->exportValue('email');
        $mail->FromName = $formulaire->exportValue('nom') . ' ' . $formulaire->exportValue('prenom');
        $mail->Subject = 'Demande d\'accréditation presse PHP Tour Luxembourg 2015';
        $mail->Body = "Une demande d'accréditation a été déposé en ligne :\n"
                    . " - titre : " . $formulaire->exportValue('titre_revue') . "\n"
                    . " - correspondant : " . $formulaire->exportValue('civilite') . " " .  $formulaire->exportValue('prenom') . " " .  $formulaire->exportValue('nom') . "\n"
                    . " - carte presse : " . $formulaire->exportValue('carte_presse') . "\n"
                    . " - adresse : " . $formulaire->exportValue('adresse') . " | " . $formulaire->exportValue('code_postal') . " " . $formulaire->exportValue('ville') . " | " . $formulaire->exportValue('id_pays') . "\n"
                    . " - téléphhone : " . $formulaire->exportValue('telephone') . "\n"
                    . " - email : " . $formulaire->exportValue('email') . "\n"
                    . " - commentaires : " . strip_tags($formulaire->exportValue('commentaires'));
        $mail->Send();
        $smarty->assign('texte', 'Merci. Votre demande d\'accréditation a été prise en compte et sera traitée prochainement.');
    } else {
        $smarty->assign('texte', 'Une erreur est survenue lors de votre inscription. Veuillez contacter le service presse dont les coordonnées apparaissent ci-dessous afin de régler le problème.');
    }
    $smarty->display('accreditation_valide.html');
} else {
    $smarty->assign('formulaire', genererFormulaire($formulaire));
    $smarty->display('accreditation.html');
}
