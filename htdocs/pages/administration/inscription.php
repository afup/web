<?php
// On supprime ce qui a déjà été écrit dans le buffer de sortie
// car on va afficher une page "indépendente"
ob_clean();
$action = verifierAction(array('ajouter'));
$smarty->assign('action', $action);

if ($action == 'ajouter') {
    require_once AFUP_CHEMIN_RACINE . 'classes/afup/AFUP_Personnes_Physiques.php';
    $personnes_physiques = new AFUP_Personnes_Physiques($bdd);

    require_once AFUP_CHEMIN_RACINE . 'classes/afup/AFUP_Personnes_Morales.php';
    $personnes_morales = new AFUP_Personnes_Morales($bdd);
    require_once AFUP_CHEMIN_RACINE . 'classes/afup/AFUP_Pays.php';
    $pays = new AFUP_Pays($bdd);

    $formulaire = &instancierFormulaire();

    $formulaire->setDefaults(array(
            'civilite' => 'M.',
            'id_pays' => 'FR',
            'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
            'niveau_apero' => AFUP_DROITS_NIVEAU_MEMBRE,
            'niveau_annuaire' => AFUP_DROITS_NIVEAU_MEMBRE,
            'etat' => AFUP_DROITS_ETAT_ACTIF,
            ));

    $formulaire->addElement('hidden' , 'inscription', 1);
    $formulaire->addElement('hidden' , 'niveau');
    $formulaire->addElement('hidden' , 'niveau_apero');
    $formulaire->addElement('hidden' , 'niveau_annuaire');
    $formulaire->addElement('hidden' , 'etat');
    $formulaire->addElement('hidden' , 'compte_svn');
	$formulaire->addElement('hidden' , 'login');

    $formulaire->addElement('header' , '' , 'Informations');
    $formulaire->addElement('select' , 'id_personne_morale' , 'Personne morale', array(null => '') + $personnes_morales->obtenirListe('id, raison_sociale', 'raison_sociale', true));
    $formulaire->addElement('select' , 'civilite' , 'Civilité' , array('M.', 'Mme', 'Mlle'));
    $formulaire->addElement('text' , 'nom' , 'Nom' , array('size' => 30, 'maxlength' => 40,
            'onblur' => 'login.value=login2.value=login.value=creerLogin(nom.value, prenom.value)'));
    $formulaire->addElement('text', 'prenom' , 'Prénom' , array('size' => 30, 'maxlength' => 40,
            'onblur' => 'login.value=login2.value=creerLogin(nom.value, prenom.value)'));
    $formulaire->addElement('text' , 'email' , 'Email' , array('size' => 30, 'maxlength' => 100));
    $formulaire->addElement('textarea', 'adresse' , 'Adresse' , array('cols' => 42, 'rows' => 10));
    $formulaire->addElement('text' , 'code_postal' , 'Code postal' , array('size' => 6, 'maxlength' => 10));
    $formulaire->addElement('text' , 'ville' , 'Ville' , array('size' => 30, 'maxlength' => 50));
    $formulaire->addElement('select' , 'id_pays' , 'Pays' , $pays->obtenirPays());
    $formulaire->addElement('text' , 'telephone_fixe' , 'Tél. fixe' , array('size' => 20, 'maxlength' => 20));
    $formulaire->addElement('text' , 'telephone_portable' , 'Tél. portable' , array('size' => 20, 'maxlength' => 20));

    $formulaire->addElement('text' , 'login2' , 'Login' , array('size' => 30, 'maxlength' => 30,
            'disabled' => 'disabled'));
    $formulaire->addElement('password', 'mot_de_passe' , 'Mot de passe' , array('size' => 30, 'maxlength' => 30));
    $formulaire->addElement('password', 'confirmation_mot_de_passe', '' , array('size' => 30, 'maxlength' => 30));
    $formulaire->addElement('header' , 'boutons' , '');
    $formulaire->addElement('submit' , 'soumettre' , ucfirst($action));

    $formulaire->addRule('nom' , 'Nom manquant' , 'required');
    $formulaire->addRule('prenom' , 'Prénom manquant' , 'required');
    $formulaire->addRule('email' , 'Email manquant' , 'required');
    $formulaire->addRule('email' , 'Email invalide' , 'email');
    $formulaire->addRule('adresse' , 'Adresse manquante' , 'required');
    $formulaire->addRule('code_postal' , 'Code postal manquant' , 'required');
    $formulaire->addRule('ville' , 'Ville manquante' , 'required');
    $formulaire->addRule('mot_de_passe', 'Mot de passe manquant', 'required');
    $formulaire->addRule(array('mot_de_passe', 'confirmation_mot_de_passe'), 'Le mot de passe et sa confirmation ne concordent pas', 'compare');

    if ($formulaire->validate()) {
        // Construction du champ niveau_modules : concaténation dse différentes valeurs
        $niveau_modules = $formulaire->exportValue('niveau_apero').
                          $formulaire->exportValue('niveau_annuaire').
                          $formulaire->exportValue('niveau_site');
    	$login = $formulaire->exportValue('login');
        $mot_de_passe = md5($formulaire->exportValue('mot_de_passe'));

        $ok = $personnes_physiques->ajouter($formulaire->exportValue('id_personne_morale'),
            $login,
            $mot_de_passe,
            $formulaire->exportValue('niveau'),
            $niveau_modules,
            $formulaire->exportValue('civilite'),
            $formulaire->exportValue('nom'),
            $formulaire->exportValue('prenom'),
            $formulaire->exportValue('email'),
            $formulaire->exportValue('adresse'),
            $formulaire->exportValue('code_postal'),
            $formulaire->exportValue('ville'),
            $formulaire->exportValue('id_pays'),
            $formulaire->exportValue('telephone_fixe'),
            $formulaire->exportValue('telephone_portable'),
            $formulaire->exportValue('etat'),
            $formulaire->exportValue('compte_svn'));

        if ($ok) {
            $motifs = array();
            $valeurs = array();
            foreach($formulaire->exportValues() as $cle => $valeur) {
                $motifs[] = '[' . $valeur . ']';
                $valeurs[] = $valeur;
            }
            $corps = str_replace($motifs, $valeurs, $conf->obtenir('mails|texte_adhesion'));

            require_once 'phpmailer/class.phpmailer.php';
            $mail = new PHPMailer;
            $mail->AddAddress($formulaire->exportValue('email'), $formulaire->exportValue('prenom') . ' ' . $formulaire->exportValue('nom'));
            $mail->From = $conf->obtenir('mails|email_expediteur');
            $mail->FromName = $conf->obtenir('mails|nom_expediteur');
            $mail->BCC = $conf->obtenir('mails|email_expediteur');
            $mail->Subject = 'Adhésion AFUP';
            $mail->Body = $corps;
            // $mail->Send();
        }

        if ($ok) {
            AFUP_Logs::log('Ajout de la personne physique ' . $formulaire->exportValue('prenom') . ' ' . $formulaire->exportValue('nom'));

            $droits->seConnecter($login, $mot_de_passe, false);

            afficherMessage('Votre inscription a été enregistrée. Veuillez maintenant payer votre cotisation. Merci. ' ,
                'index.php?page=membre_cotisation&hash=' . $droits->obtenirHash());
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de la création de votre compte. Veuillez recommencer. Merci.');
        }
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));
}

?>