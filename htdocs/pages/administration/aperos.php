<?php

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer'));
$tris_valides = array('date', 'organisateur', 'ville');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

require_once AFUP_CHEMIN_RACINE . 'classes/afup/AFUP_Aperos.php';
$aperos = new AFUP_Aperos($bdd);

if ($action == 'lister') {

    // Valeurs par dfaut des paramtres de tri
    $list_ordre = 'date DESC';
    $list_sens = 'asc';
    $list_associatif = false;
    $list_filtre = false;

    // Modification des paramtres de tri en fonction des demandes passes en GET
    if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides)
        && isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
        $list_ordre = $_GET['tri'] . ' ' . $_GET['sens'];
    }
    
    // Mise en place de la liste dans le scope de smarty
    $evenements = $aperos->obtenirListe($list_ordre, $list_associatif, $list_filtre);
    $smarty->assign('evenements', $evenements);

} elseif ($action == 'supprimer') {
    if ($aperos->supprimer($_GET['id'])) {
        AFUP_Logs::log('Suppression de l\'apéro ' . $_GET['id']);
        afficherMessage('L\'apéro a été supprimé', 'index.php?page=aperos&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de l\'apéro', 'index.php?page=aperos&action=lister', true);
    }

} else {
    $formulaire = &instancierFormulaire();
    if ($action == 'ajouter') {
//        $formulaire->setDefaults(array('id_pays' => 'FR',
//                                       'SIREN-Test'    => 'KO',    
//                                       'etat'    => AFUP_DROITS_ETAT_ACTIF));
    } else {
        $champs = $aperos->obtenir($_GET['id']);
        $formulaire->setDefaults($champs);    
    }


    $formulaire->addElement('header'  , ''                   , 'Informations');
    $formulaire->addElement('text'    , 'date'               , 'Date'         , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('select'  , 'ID_organisateur'    , 'Organisateur' , $aperos->obtenirOrganisateurs());
    $formulaire->addElement('select'  , 'ID_ville'           , 'Ville'        , $aperos->obtenirVilles());
    $formulaire->addElement('text'    , 'lieu'               , 'Lieu'         , array('size' => 30, 'maxlength' => 40));

    $formulaire->addElement('header'  , ''                   , 'Paramètres');
    $formulaire->addElement('select'  , 'valide'             , 'Etat'         , array(AFUP_APERO_ETAT_ACTIF   => 'Actif',
                                                                                      AFUP_APERO_ETAT_INACTIF => 'Inactif'));
    $formulaire->addElement('text'    , 'NB_messages'        , 'NB_messages'  , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'NB_phpautes'        , 'NB_phpautes'  , array('size' => 30, 'maxlength' => 40));
    
    $formulaire->addElement('header'  , 'boutons'            , '');
    $formulaire->addElement('submit'  , 'soumettre'          , ucfirst($action));
    
    if ($formulaire->validate()) {
        if ($action == 'ajouter') {
            $ok = $aperos->ajouter($formulaire->exportValue('ID_organisateur'),
                                   $formulaire->exportValue('ID_ville'),
                                   $formulaire->exportValue('date'),
                                   $formulaire->exportValue('lieu'),
                                   $formulaire->exportValue('valide'),
                                   $formulaire->exportValue('NB_messages'),
                                   $formulaire->exportValue('NB_phpautes'));
        } else {
            $ok = $aperos->modifier($_GET['id'],
                                    $formulaire->exportValue('ID_organisateur'),
                                    $formulaire->exportValue('ID_ville'),
                                    $formulaire->exportValue('date'),
                                    $formulaire->exportValue('lieu'),
                                    $formulaire->exportValue('valide'),
                                    $formulaire->exportValue('NB_messages'),
                                    $formulaire->exportValue('NB_phpautes'));
        }
        
        if ($ok) {
            if ($action == 'ajouter') {
                AFUP_Logs::log('Ajout de l\'apéro du ' . $formulaire->exportValue('date'));
            } else {
                AFUP_Logs::log('Modification de l\'apéro du ' . $formulaire->exportValue('date') . ' (' . $_GET['id'] . ')');
            }            
            afficherMessage('L\'apéro a été ' . (($action == 'ajouter') ? 'ajouté' : 'modifié'), 'index.php?page=aperos&action=lister');    
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'apéro');    
        }    
    } 
    
    $smarty->assign('formulaire', genererFormulaire($formulaire));
}

?>