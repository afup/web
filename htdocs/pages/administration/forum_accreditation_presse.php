<?php

// Impossible to access the file itself
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer'));
$tris_valides = array('date', 'titre_revue', 'nom_forum', 'nom', 'prenom');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Pays.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Accreditation_Presse.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';
$pays = new AFUP_Pays($bdd);
$accreditations = new AFUP_Accreditation_Presse($bdd);
$forums = new AFUP_Forum($bdd);

if ($action == 'lister') {
    // Valeurs par dfaut des paramtres de tri
    $list_ordre = 'date DESC';
    $list_sens = 'asc';
    $list_associatif = false;

    // Modification des paramtres de tri en fonction des demandes passes en GET
    if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides)
        && isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
        $list_ordre = $_GET['tri'] . ' ' . $_GET['sens'];
    }

    // Mise en place de la liste dans le scope de smarty
    $journalistes = $accreditations->obtenirListe($list_ordre, $list_associatif);
    $smarty->assign('journalistes', $journalistes);
} elseif ($action == 'supprimer') {
    if ($accreditations->supprimer($_GET['id'])) {
        AFUP_Logs::log('Suppression de l\'accréditation ' . $_GET['id']);
        afficherMessage('L\'accréditation a été supprimée', 'index.php?page=forum_accreditation_presse&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de l\'accréditation', 'index.php?page=forum_accreditation_presse&action=lister', true);
    }
} else {
    $formulaire = &instancierFormulaire();
    if ($action == 'ajouter') {
		$formulaire->setDefaults(array('civilite' => 'M.',
									   'id_pays'  => 'FR'));
    } else {
        $champs = $accreditations->obtenir($_GET['id']);

        $formulaire->setDefaults($champs);

    	if (isset($champs) && isset($champs['id'])) {
    	    $_GET['id'] = $champs['id'];
    	}

        $formulaire->addElement('hidden', 'id', $_GET['id']);
    }

    $formulaire->addElement('header'  , ''            , 'Demande d\'accr&eacute;ditation');
    $formulaire->addElement('select'  , 'id_forum'    , 'Forum'          , $forums->obtenirListe(null,'id, titre', 'titre', true));
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
    $formulaire->addElement('checkbox', 'valide'      , 'Valide');
    $formulaire->addElement('submit'  , 'soumettre'   , 'Soumettre');

    $formulaire->addRule('id_forum' , 'Forum manquant' , 'required');
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
        if ($action == 'ajouter') {
            $ok = $accreditations->ajouter(null,
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
                                   $formulaire->exportValue('id_forum'),
                                   $formulaire->exportValue('valide'));
        } else {
            $ok = $accreditations->modifier($formulaire->exportValue('id'),
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
                                    $formulaire->exportValue('id_forum'),
                                    $formulaire->exportValue('valide'));
        }

        if ($ok) {
            if ($action == 'ajouter') {
                AFUP_Logs::log('Ajout de l\'accréditation de ' . $formulaire->exportValue('prenom') . ' ' . $formulaire->exportValue('nom'));
            } else {
                AFUP_Logs::log('Modification de l\'accréditation de ' . $formulaire->exportValue('prenom') . ' ' . $formulaire->exportValue('nom') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('L\'accréditation a été ' . (($action == 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=forum_accreditation_presse&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'accréditation');
        }
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));
}