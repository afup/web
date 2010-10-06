<?php

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer'));
$tris_valides = array('titre', 'date');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Site.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Personnes_Physiques.php';

$articles = new AFUP_Site_Articles($bdd);
$personnes_physiques = new AFUP_Personnes_Physiques($bdd);

if ($action == 'lister') {
    $list_champs     = '*';
    $list_ordre      = 'date';
    $list_sens       = 'desc';
    $list_filtre     = false;

    if (isset($_GET['sens']) && in_array($_GET['sens'], array_keys($sens_valides))) {
        $list_sens = $_GET['sens'];
    } else {
        $_GET['sens'] = $list_sens;
    }
    if (isset($_GET['tri']) && in_array($_GET['tri'], array_keys($tris_valides))) {
        $list_ordre = $_GET['tri'];
    } else {
        $_GET['tri'] = $list_ordre;
    }
    if (isset($_GET['filtre'])) {
        $list_filtre = $_GET['filtre'];
    } else {
        $_GET['filtre'] = $list_filtre;
    }

    // Mise en place de la liste dans le scope de smarty
    $smarty->assign('articles', $articles->obtenirListe($list_champs, $list_ordre.' '.$list_sens, $list_filtre));

} elseif ($action == 'supprimer') {
    $article = new AFUP_Site_Article($_GET['id']);
    if ($article->supprimer()) {
        AFUP_Logs::log('Suppression de l\'article ' . $_GET['id']);
        afficherMessage('L\'article a �t� supprim�', 'index.php?page=site_articles&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de l\'article', 'index.php?page=site_articles&action=lister', true);
    }

} else {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $article = new AFUP_Site_Article($id);
    $rubriques = new AFUP_Site_Rubriques();

    $formulaire = &instancierFormulaire();
    if ($action == 'ajouter') {
        $formulaire->setDefaults(array('date' => time(),
                                       'position' => 0,
                                       'id_personne_physique' => $droits->obtenirIdentifiant(),
                                       'etat' => 0));
    } else {
        $champs = $article->charger();
        $formulaire->setDefaults($article->exportable());
    }

    $formulaire->addElement('header'  , ''                         , 'Article');
    $formulaire->addElement('textarea', 'surtitre'                 , 'Surtitre'        , array('cols' => 42, 'rows'      => 5, 'class' => 'tinymce'));
    $formulaire->addElement('text'    , 'titre'                    , 'Titre'           , array('size' => 60, 'maxlength' => 255));
    $formulaire->addElement('textarea', 'descriptif'               , 'Descriptif'      , array('cols' => 42, 'rows'      => 10, 'class' => 'tinymce'));
    $formulaire->addElement('textarea', 'chapeau'                  , 'Chapeau'         , array('cols' => 42, 'rows'      => 10, 'class' => 'tinymce'));
    $formulaire->addElement('textarea', 'contenu'                  , 'Contenu'         , array('cols' => 42, 'rows'      => 20, 'class' => 'tinymce'));
        
    $formulaire->addElement('header'  , ''                         , 'M&eacute;ta-donn&eacute;es');
    $formulaire->addElement('text'    , 'raccourci'                , 'Raccourci'      , array('size' => 60, 'maxlength' => 255));
    $formulaire->addElement('select'  , 'id_site_rubrique'         , 'Rubrique'       , array(null => '' ) + $rubriques->obtenirListe('id, nom', 'nom', true));
    $formulaire->addElement('select'  , 'id_personne_physique'     , 'Auteur'         , array(null => '' ) + $personnes_physiques->obtenirListe('id, CONCAT(prenom, " ", nom) as nom', 'nom', false, false, true));
    $formulaire->addElement('date'    , 'date'                     , 'Date'           , array('language' => 'fr', 'minYear' => 2001, 'maxYear' => date('Y')));
    $formulaire->addElement('select'  , 'position'                 , 'Position'       , $article->positionable());
    $formulaire->addElement('select'  , 'etat'                     , 'Etat'           , array(-1 => 'Hors ligne', 0 => 'En attente', 1 => 'En ligne'));
    
    $formulaire->addElement('header'  , 'boutons'                  , '');
    $formulaire->addElement('submit'  , 'soumettre'                , ucfirst($action));

    $formulaire->addRule('titre'       , 'Titre manquant'       , 'required');
    $formulaire->addRule('contenu'     , 'Contenu manquant'     , 'required');
    $formulaire->addRule('raccourci'   , 'Raccourci manquant'   , 'required');

    if ($formulaire->validate()) {
        $article->id_site_rubrique = $formulaire->exportValue('id_site_rubrique');
        $article->id_personne_physique = $formulaire->exportValue('id_personne_physique');
        $article->surtitre = $formulaire->exportValue('surtitre');
        $article->titre = $formulaire->exportValue('titre');
        $article->raccourci = $formulaire->exportValue('raccourci');
        $article->descriptif = $formulaire->exportValue('descriptif');
        $article->chapeau = $formulaire->exportValue('chapeau');
        $article->contenu = $formulaire->exportValue('contenu');
        $article->position = $formulaire->exportValue('position');
        $date = $formulaire->exportValue('date');
        $article->date = mktime(0, 0, 0, $date['M'], $date['d'], $date['Y']);
        $article->etat = $formulaire->exportValue('etat');

        if ($action == 'ajouter') {
            $ok = $article->inserer();
        } else {
            $ok = $article->modifier();
        }

        if ($ok) {
            if ($action == 'ajouter') {
                AFUP_Logs::log('Ajout de l\'article ' . $formulaire->exportValue('titre'));
            } else {
                AFUP_Logs::log('Modification de l\'article ' . $formulaire->exportValue('titre') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('l\'article a �t� ' . (($action == 'ajouter') ? 'ajout�' : 'modifi�'), 'index.php?page=site_articles&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'article');
        }
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));
}
