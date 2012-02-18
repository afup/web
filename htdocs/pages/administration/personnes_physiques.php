<?php

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer', 'envoi_mdp'));
$tris_valides = array('nom' => 'nom <sens>, prenom',
    'prenom' => 'prenom <sens>, nom',
    'etat' => 'etat <sens>, prenom, nom');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Personnes_Physiques.php';
$personnes_physiques = new AFUP_Personnes_Physiques($bdd);

if ($action == 'lister') {
    // Valeurs par défaut des paramètres de tri
    $list_champs = '*';
    $list_ordre = 'nom, prenom';
    $list_sens = 'asc';
    $list_filtre = false;
    // Modification des paramètres de tri en fonction des demandes passées en GET
    if (isset($_GET['tri']) && in_array($_GET['tri'], array_keys($tris_valides)) && isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
        $list_ordre = str_replace('<sens>', $_GET['sens'], $tris_valides[$_GET['tri']]);
    }
    if (isset($_GET['filtre'])) {
        $list_filtre = $_GET['filtre'];
    }
    // Mise en place de la liste dans le scope de smarty
    $smarty->assign('personnes', $personnes_physiques->obtenirListe($list_champs, $list_ordre, $list_filtre));
} elseif ($action == 'supprimer') {
    if ($personnes_physiques->supprimer($_GET['id'])) {
        AFUP_Logs::log('Suppression de la personne physique ' . $_GET['id']);
        afficherMessage('La personne physique a été supprimée', 'index.php?page=personnes_physiques&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de la personne physique', 'index.php?page=personnes_physiques&action=lister', true);
    }
} elseif ($action == 'envoi_mdp') {
    if ($personnes_physiques->envoyerMotDePasse(null, null, $_GET['id'])) {
        AFUP_Logs::log('Envoi d\'un nouveau mot de passe à la personne physique ' . $_GET['id']);
        afficherMessage('Un nouveau mot de passe a été envoyé à la personne physique', 'index.php?page=personnes_physiques&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de l\'envoi d\'un nouveau mot de passe à la personne physique', 'index.php?page=personnes_physiques&action=lister', true);
    }
} else {
    require_once 'Afup/AFUP_Personnes_Morales.php';
    $personnes_morales = new AFUP_Personnes_Morales($bdd);
    require_once 'Afup/AFUP_Pays.php';
    $pays = new AFUP_Pays($bdd);

    $formulaire = &instancierFormulaire();
    if ($action == 'ajouter') {
        $mot_de_passe = md5(time());
        $formulaire->setDefaults(array('civilite' => 'M.',
                'id_pays' => 'FR',
                'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
                'niveau_apero' => AFUP_DROITS_NIVEAU_MEMBRE,
                'niveau_annuaire' => AFUP_DROITS_NIVEAU_MEMBRE,
                'niveau_forum' => AFUP_DROITS_NIVEAU_MEMBRE,
                'niveau_site' => AFUP_DROITS_NIVEAU_MEMBRE,
        		'etat' => AFUP_DROITS_ETAT_INACTIF,
                'mot_de_passe' => '',
                'confirmation_mot_de_passe' => ''));
    } else {
        $champs = $personnes_physiques->obtenir($_GET['id']);
        unset($champs['mot_de_passe']);
        $formulaire->setDefaults($champs);
    }

    $formulaire->addElement('header' , '' , 'Informations');
    $formulaire->addElement('select' , 'id_personne_morale' , 'Personne morale', array(null => '') + $personnes_morales->obtenirListe('id, raison_sociale', 'raison_sociale', true));
    if ($action == 'modifier') {
        $formulaire->addElement('static', 'note' , '    ' , '<a href="#" onclick="voirPersonneMorale(); return false;" title="Voir la personne morale">Voir la personne morale</a>');
    }

    $formulaire->addElement('select' , 'civilite' , 'Civilité' , array('M.', 'Mme', 'Mlle'));

    if ($action == 'ajouter') {
        $formulaire->addElement('text', 'nom' , 'Nom' , array('size' => 30, 'maxlength' => 40,
                'onblur' => 'login.value=login.value=creerLogin(nom.value, prenom.value)'));
        $formulaire->addElement('text', 'prenom' , 'Prénom' , array('size' => 30, 'maxlength' => 40,
                'onblur' => 'login.value=login.value=creerLogin(nom.value, prenom.value)'));
    } else {
        $formulaire->addElement('text' , 'nom' , 'Nom' , array('size' => 30, 'maxlength' => 40));
        $formulaire->addElement('text', 'prenom' , 'Prénom' , array('size' => 30, 'maxlength' => 40));
    }

    $formulaire->addElement('text' , 'email' , 'Email' , array('size' => 30, 'maxlength' => 100));
    $formulaire->addElement('textarea', 'adresse' , 'Adresse' , array('cols' => 42, 'rows' => 10));
    $formulaire->addElement('text' , 'code_postal' , 'Code postal' , array('size' => 6, 'maxlength' => 10));
    $formulaire->addElement('text' , 'ville' , 'Ville' , array('size' => 30, 'maxlength' => 50));
    $formulaire->addElement('select' , 'id_pays' , 'Pays' , $pays->obtenirPays());
    $formulaire->addElement('text' , 'telephone_fixe' , 'Tél. fixe' , array('size' => 20, 'maxlength' => 20));
    $formulaire->addElement('text' , 'telephone_portable' , 'Tél. portable' , array('size' => 20, 'maxlength' => 20));

    $formulaire->addElement('header' , '' , 'Paramètres');
    $formulaire->addElement('text' , 'compte_svn' , 'Compte SVN' , array('size' => 20, 'maxlength' => 20));
    $formulaire->addElement('select' , 'niveau' , 'Niveau' , array(AFUP_DROITS_NIVEAU_MEMBRE => 'Membre',
            AFUP_DROITS_NIVEAU_REDACTEUR => 'Rédacteur',
            AFUP_DROITS_NIVEAU_ADMINISTRATEUR => 'Administrateur'));
    $formulaire->addElement('select' , 'niveau_apero' , 'Apéros PHP' , array(AFUP_DROITS_NIVEAU_MEMBRE => '--',
            AFUP_DROITS_NIVEAU_ADMINISTRATEUR => 'Gestionnaire'));
    $formulaire->addElement('select' , 'niveau_annuaire' , 'Annuaire des prestataires', array(AFUP_DROITS_NIVEAU_MEMBRE => '--',
            AFUP_DROITS_NIVEAU_ADMINISTRATEUR => 'Gestionnaire'));
    $formulaire->addElement('select' , 'niveau_forum' , 'Forum PHP & PHP Tour', array(AFUP_DROITS_NIVEAU_MEMBRE => '--',
            AFUP_DROITS_NIVEAU_ADMINISTRATEUR => 'Gestionnaire'));
    $formulaire->addElement('select' , 'niveau_site' , 'Site web', array(AFUP_DROITS_NIVEAU_MEMBRE => '--',
            AFUP_DROITS_NIVEAU_ADMINISTRATEUR => 'Gestionnaire'));
            
    $formulaire->addElement('select' , 'etat' , 'Etat' , array(AFUP_DROITS_ETAT_NON_FINALISE => 'Non finalisé',
	    AFUP_DROITS_ETAT_ACTIF => 'Actif',
            AFUP_DROITS_ETAT_INACTIF => 'Inactif'));
    $formulaire->addElement('text' , 'login' , 'Login' , array('size' => 30, 'maxlength' => 30));
    if ($action == 'modifier') {
        $formulaire->addElement('static', 'note' , '    ' , 'Ne renseignez le mot de passe et sa confirmation que si vous souhaitez le changer');
    } else {
        $formulaire->addElement('static', 'note' , '    ' , 'Ne renseignez le mot de passe et sa confirmation que si vous souhaitez le définir');
    }
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
    $formulaire->addRule('login' , 'Login manquant' , 'required');
    $formulaire->addRule(array('mot_de_passe', 'confirmation_mot_de_passe'), 'Le mot de passe et sa confirmation ne concordent pas', 'compare');

    if ($formulaire->validate()) {
        if ($action == 'ajouter') {
            // Construction du champ niveau_modules : concaténation dse différentes valeurs
            $niveau_modules = $formulaire->exportValue('niveau_apero').
                              $formulaire->exportValue('niveau_annuaire').
                              $formulaire->exportValue('niveau_site').
                              $formulaire->exportValue('niveau_forum');

            $ok = $personnes_physiques->ajouter($formulaire->exportValue('id_personne_morale'),
                $formulaire->exportValue('login'),
                md5(time()),
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
        } else {
            /**
            * Niveau modules : concaténation
            */
            $niveau_modules = $formulaire->exportValue('niveau_apero').
                              $formulaire->exportValue('niveau_annuaire').
                              $formulaire->exportValue('niveau_site').
                              $formulaire->exportValue('niveau_forum');

            $ok = $personnes_physiques->modifier($_GET['id'],
                $formulaire->exportValue('id_personne_morale'),
                $formulaire->exportValue('login'),
                $formulaire->exportValue('mot_de_passe'),
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
        }

        if ($ok) {
            if ($action == 'ajouter') {
                AFUP_Logs::log('Ajout de la personne physique ' . $formulaire->exportValue('prenom') . ' ' . $formulaire->exportValue('nom'));
            } else {
                AFUP_Logs::log('Modification de la personne physique ' . $formulaire->exportValue('prenom') . ' ' . $formulaire->exportValue('nom') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('La personne physique a été ' . (($action == 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=personnes_physiques&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de la personne physique');
        }
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));
}