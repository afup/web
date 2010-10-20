<?php

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer'));
$tris_valides = array('raison_sociale', 'etat');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Personnes_Morales.php';
$personnes_morales = new AFUP_Personnes_Morales($bdd);

if ($action == 'lister') {
    $list_champs = '*';
    $list_ordre = 'raison_sociale';
    $list_sens = 'asc';
    $list_associatif = false;
    $list_filtre = false;

    if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides)
        && isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
        $list_ordre = $_GET['tri'] . ' ' . $_GET['sens'];
    }
    if (isset($_GET['filtre'])) {
        $list_filtre = $_GET['filtre'];
    }

    $smarty->assign('personnes', $personnes_morales->obtenirListe($list_champs, $list_ordre, $list_associatif, $list_filtre));
} elseif ($action == 'supprimer') {
    if ($personnes_morales->supprimer($_GET['id'])) {
        AFUP_Logs::log('Suppression de la personne morale ' . $_GET['id']);
        afficherMessage('La personne morale a été supprimée', 'index.php?page=personnes_morales&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de la personne morale', 'index.php?page=personnes_morales&action=lister', true);
    }
} else {
    require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Personnes_Physiques.php';
    $personnes_physiques = new AFUP_Personnes_Physiques($bdd);
    $personnes_physiques_liste = empty($_GET['id'])? array() : $personnes_physiques->obtenirListe('*', 'nom, prenom', false, $_GET['id']);

    require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Pays.php';
    $pays = new AFUP_Pays($bdd);

    $formulaire = &instancierFormulaire();
    if ($action == 'ajouter') {
        $formulaire->setDefaults(array('civilite' => 'M.',
                                       'id_pays' => 'FR',
                                       'niveau'  => AFUP_DROITS_NIVEAU_REDACTEUR,
                                       'etat'    => AFUP_DROITS_ETAT_ACTIF));
    } else {
        $champs = $personnes_morales->obtenir($_GET['id']);
        unset($champs['mot_de_passe']);
        $formulaire->setDefaults($champs);
    }

    $formulaire->addElement('header'  , ''                   , 'Informations');
    $formulaire->addElement('text'    , 'raison_sociale'     , 'Raison sociale' , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'siret'              , 'Siret'          , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('textarea', 'adresse'            , 'Adresse'        , array('cols' => 42, 'rows'      => 10));
    $formulaire->addElement('text'    , 'code_postal'        , 'Code postal'    , array('size' =>  6, 'maxlength' => 10));
    $formulaire->addElement('text'    , 'ville'              , 'Ville'          , array('size' => 30, 'maxlength' => 50));
    $formulaire->addElement('select'  , 'id_pays'            , 'Pays'           , $pays->obtenirPays());

    $formulaire->addElement('header'  , ''                   , 'Contact administratif');
    $formulaire->addElement('select'  , 'civilite'           , 'Civilité'       , array('M.', 'Mme', 'Mlle'));
    $formulaire->addElement('text'    , 'nom'                , 'Nom'            , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'prenom'             , 'Prénom'         , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'email'              , 'Email'          , array('size' => 30, 'maxlength' => 100));
    $formulaire->addElement('text'    , 'telephone_fixe'     , 'Tél. fixe'      , array('size' => 20, 'maxlength' => 20));
    $formulaire->addElement('text'    , 'telephone_portable' , 'Tél. portable'  , array('size' => 20, 'maxlength' => 20));
    if($action != 'ajouter') {
        $formulaire->addElement('header'  , ''                   , 'Personnes physiques associées');
        foreach ($personnes_physiques_liste as $personne_physique) {
            $nom = $personne_physique['nom'] . ' ' . $personne_physique['prenom'][0];
            empty($personne_physique['etat']) and $nom = "<del>$nom</del>";
            $formulaire->addElement('static', 'info', $nom . '.',
		    '<a href="index.php?page=personnes_physiques&action=modifier&id=' . $personne_physique['id'] . '" title="Voir la fiche de la personne physique">Voir la fiche</a>');
        }
    }
    $formulaire->addElement('header'  , ''                   , 'Paramétres');
    $formulaire->addElement('select'  , 'etat'               , 'Etat'        , array(AFUP_DROITS_ETAT_ACTIF   => 'Actif',
                                                                                   AFUP_DROITS_ETAT_INACTIF => 'Inactif'));

    $formulaire->addElement('header'  , 'boutons'            , '');
    $formulaire->addElement('submit'  , 'soumettre'          , ucfirst($action));

    $formulaire->addRule('nom'         , 'Nom manquant'         , 'required');
    $formulaire->addRule('prenom'      , 'Prénom manquant'      , 'required');
    $formulaire->addRule('email'       , 'Email manquant'       , 'required');
    $formulaire->addRule('email'       , 'Email invalide'       , 'email');
    $formulaire->addRule('raison_sociale', 'Raison sociale manquante', 'required');
    $formulaire->addRule('adresse'       , 'Adresse manquante'       , 'required');
    $formulaire->addRule('code_postal'   , 'Code postal manquant'    , 'required');
    $formulaire->addRule('ville'         , 'Ville manquante'         , 'required');

    if ($formulaire->validate()) {
        if ($action == 'ajouter') {
            $ok = $personnes_morales->ajouter($formulaire->exportValue('civilite'),
                                              $formulaire->exportValue('nom'),
                                              $formulaire->exportValue('prenom'),
                                              $formulaire->exportValue('email'),
                                              $formulaire->exportValue('raison_sociale'),
                                              $formulaire->exportValue('siret'),
                                              $formulaire->exportValue('adresse'),
                                              $formulaire->exportValue('code_postal'),
                                              $formulaire->exportValue('ville'),
                                              $formulaire->exportValue('id_pays'),
                                              $formulaire->exportValue('telephone_fixe'),
                                              $formulaire->exportValue('telephone_portable'),
                                              $formulaire->exportValue('etat'));
        } else {
            $ok = $personnes_morales->modifier($_GET['id'],
                                               $formulaire->exportValue('civilite'),
                                               $formulaire->exportValue('nom'),
                                               $formulaire->exportValue('prenom'),
                                               $formulaire->exportValue('email'),
                                               $formulaire->exportValue('raison_sociale'),
                                               $formulaire->exportValue('siret'),
                                               $formulaire->exportValue('adresse'),
                                               $formulaire->exportValue('code_postal'),
                                               $formulaire->exportValue('ville'),
                                               $formulaire->exportValue('id_pays'),
                                               $formulaire->exportValue('telephone_fixe'),
                                               $formulaire->exportValue('telephone_portable'),
                                               $formulaire->exportValue('etat'));
        }

        if ($ok) {
            if ($action == 'ajouter') {
                AFUP_Logs::log('Ajout de la personne morale ' . $formulaire->exportValue('raison_sociale'));
            } else {
                AFUP_Logs::log('Modification de la personne morale ' . $formulaire->exportValue('raison_sociale') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('La personne morale a été ' . (($action == 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=personnes_morales&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de la personne morale');
        }
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));
}
