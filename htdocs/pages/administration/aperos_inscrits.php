<?php

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer'));
$tris_valides = array('nom', 'pseudo');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Aperos_Inscrits.php';
$aperos_inscrits = new AFUP_Aperos_Inscrits($bdd);

if ($action == 'lister') {

    // Valeurs par dfaut des paramtres de tri
    $list_ordre = 'nom ASC';
    $list_sens = 'asc';
    $list_associatif = false;
    $list_filtre = false;

    // Modification des paramtres de tri en fonction des demandes passes en GET
    if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides)
        && isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
        $list_ordre = $_GET['tri'] . ' ' . $_GET['sens'];
    }
    
    // Mise en place de la liste dans le scope de smarty
    $inscrits = $aperos_inscrits->obtenirListe($list_ordre, $list_associatif, $list_filtre);
    $smarty->assign('inscrits', $inscrits);

} elseif ($action == 'supprimer') {
    if ($aperos_inscrits->supprimer($_GET['id'])) {
        AFUP_Logs::log('Suppression de l\'inscrit ' . $_GET['id'] . ' aux apéros PHP');
        afficherMessage('L\'inscrit aux apéros PHP a été supprimé', 'index.php?page=aperos_inscrits&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de l\'inscrit aux apéros PHP', 'index.php?page=aperos_inscrits&action=lister', true);
    }

} else {
    $formulaire = &instancierFormulaire();
    if ($action == 'ajouter') {
//        $formulaire->setDefaults(array('id_pays' => 'FR',
//                                       'SIREN-Test'    => 'KO',    
//                                       'etat'    => AFUP_DROITS_ETAT_ACTIF));
    } else {
        $champs = $aperos_inscrits->obtenir($_GET['id']);
        $formulaire->setDefaults($champs);    
    }


    $formulaire->addElement('header'  , ''                   , 'Inscrit');
    $formulaire->addElement('text'    , 'nom'                , 'Nom'          , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'prenom'             , 'Prénom'       , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'pseudo'             , 'Pseudo'       , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'passwd'             , 'Mot de passe' , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'mail'               , 'Email'        , array('size' => 30, 'maxlength' => 40));

    $formulaire->addElement('header'  , ''                   , 'Informations');
    $formulaire->addElement('text'    , 'age'                , 'Age'                , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'ville'              , 'Ville'              , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'id_dept'            , 'Département'        , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'site_web'            , 'Site web'           , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'photo'              , 'Photo'              , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'mailinglist'        , 'Liste de diffusion' , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'descriptif'         , 'Descriptif'         , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'date_entree'        , 'Date d\'entrée'     , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'clef'               , 'Clef'               , array('size' => 30, 'maxlength' => 40));
    
    $formulaire->addElement('header'  , 'boutons'            , '');
    $formulaire->addElement('submit'  , 'soumettre'          , ucfirst($action));
    
    if ($formulaire->validate()) {
        if ($action == 'ajouter') {
            $ok = $aperos_inscrits->ajouter($formulaire->exportValue('pseudo'),
                                            $formulaire->exportValue('passwd'),
                                            $formulaire->exportValue('nom'),
                                            $formulaire->exportValue('prenom'),
                                            $formulaire->exportValue('age'),
                                            $formulaire->exportValue('ville'),
                                            $formulaire->exportValue('id_dept'),
                                            $formulaire->exportValue('mail'),
                                            $formulaire->exportValue('site_web'),
                                            $formulaire->exportValue('photo'),
                                            $formulaire->exportValue('mailinglist'),
                                            $formulaire->exportValue('descriptif'),
                                            $formulaire->exportValue('date_entree'),
                                            $formulaire->exportValue('clef'));
        } else {
            $ok = $aperos_inscrits->modifier($_GET['id'],
                                             $formulaire->exportValue('pseudo'),
                                             $formulaire->exportValue('passwd'),
                                             $formulaire->exportValue('nom'),
                                             $formulaire->exportValue('prenom'),
                                             $formulaire->exportValue('age'),
                                             $formulaire->exportValue('ville'),
                                             $formulaire->exportValue('id_dept'),
                                             $formulaire->exportValue('mail'),
                                             $formulaire->exportValue('site_web'),
                                             $formulaire->exportValue('photo'),
                                             $formulaire->exportValue('mailinglist'),
                                             $formulaire->exportValue('descriptif'),
                                             $formulaire->exportValue('date_entree'),
                                             $formulaire->exportValue('clef'));
        }
        
        if ($ok) {
            if ($action == 'ajouter') {
                AFUP_Logs::log('Ajout de l\'inscrit ' . $formulaire->exportValue('pseudo') . ' aux apéros PHP ');
            } else {
                AFUP_Logs::log('Modification de l\'inscrit ' . $formulaire->exportValue('pseudo') . ' (' . $_GET['id'] . ') aux apéros PHP ');
            }            
            afficherMessage('L\'inscrit aux apéros PHP a été ' . (($action == 'ajouter') ? 'ajouté' : 'modifié'), 'index.php?page=aperos_inscrits&action=lister');    
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'inscrit aux apéros PHP ');    
        }    
    } 
    
    $smarty->assign('formulaire', genererFormulaire($formulaire));
}

?>