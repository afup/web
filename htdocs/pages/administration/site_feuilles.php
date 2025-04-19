<?php

declare(strict_types=1);

// Impossible to access the file itself
use Afup\Site\Corporate\Feuille;
use Afup\Site\Corporate\Feuilles;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(['lister', 'ajouter', 'modifier', 'supprimer']);
$tris_valides = ['titre', 'date'];
$sens_valides = ['asc', 'desc'];
$smarty->assign('action', $action);



$feuilles = new Feuilles($bdd);

if ($action == 'lister') {
    $f = [];
    $list_champs = '*';
    $list_ordre  = 'date';
    $list_sens   = 'desc';
    $list_filtre = false;

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

    // Mise en place de la liste dans le scope de smarty
    $smarty->assign('feuilles', $feuilles->obtenirListe($list_champs, $list_ordre . ' ' . $list_sens, $list_filtre));
} elseif ($action == 'supprimer') {
    $feuille = new Feuille($_GET['id']);
    if ($feuille->supprimer()) {
        Logs::log('Suppression de la feuille ' . $_GET['id']);
        afficherMessage('La feuille a été supprimée', 'index.php?page=site_feuilles&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de la feuille', 'index.php?page=site_feuilles&action=lister', true);
    }
} else { // ajouter | modifier
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $feuille = new Feuille($id);

    $formulaire = instancierFormulaire();
    if ($action == 'ajouter') {
        $formulaire->setDefaults(['date' => time(),
                                       'position' => 0,
                                       'id_personne_physique' => $droits->obtenirIdentifiant(),
                                       'etat' => 0]);
    } else {
        $feuille->charger();
        $formulaire->setDefaults($feuille->exportable());
    }

    $formulaire->addElement('header'  , ''                     , 'feuille');
    $formulaire->addElement('select'  , 'id_parent'            , 'Parent'    , [null => '' ] + $feuilles->obtenirListe('id, nom', 'nom', true));
    $formulaire->addElement('text'	  , 'nom'                  , 'Nom'       , ['size' => 60, 'maxlength' => 255]);
    $formulaire->addElement('text'	  , 'lien'                 , 'Lien'      , ['size' => 60, 'maxlength' => 255]);
    $formulaire->addElement('text'	  , 'alt'                  , 'Description', ['size' => 60, 'maxlength' => 255]);
    $file = $formulaire->addElement('file', 'nouvelle-image'  , 'Image');
    $formulaire->addElement('static'  , 'note'                 , ''          , '<img src="../../templates/site/images/' . $feuille->image . '" />');
    $formulaire->addElement('text'	  , 'image_alt'            , 'Texte alternatif pour l\'image', ['size' => 60, 'maxlength' => 255]);
    $formulaire->addElement('hidden'  , 'image');
    $formulaire->addElement('date'    , 'date'                 , 'Date'      , ['language' => 'fr', 'minYear' => 2001, 'maxYear' => date('Y')]);
    $formulaire->addElement('select'  , 'position'             , 'Position'  , $feuille->positionable());
    $formulaire->addElement('select'  , 'etat'                 , 'Etat'      , [-1 => 'Hors ligne', 0 => 'En attente', 1 => 'En ligne']);
    $formulaire->addElement('textarea'  , 'patterns'                 , 'Patterns URL');
    $formulaire->addElement('header'  , 'boutons'              , '');
    $formulaire->addElement('submit'  , 'soumettre'            , ucfirst($action));

    $formulaire->addRule('nom'        , 'Nom manquant'         , 'required');
    $formulaire->addRule('contenu'    , 'Contenu manquant'     , 'required');
    $formulaire->addRule('lien'       , 'Lien manquant'        , 'required');
    $formulaire->addRule('image'      , 'Mimetype'             , 'mimetype', ['jpg','jpeg','gif','png']);

    if ($file->isUploadedFile()) {
        $values = $file->getValue();
        if ($values['error'] == 0) {
            $file->moveUploadedFile('../../templates/site/images/', $values['name']);
            $feuille->image = $values['name'];
        } else {
            $feuille->image = $formulaire->exportValue('image');
        }
    }
    if ($formulaire->validate()) {
        $feuille->id_parent = $formulaire->exportValue('id_parent');
        $feuille->nom = $formulaire->exportValue('nom');
        $feuille->lien = $formulaire->exportValue('lien');
        $feuille->alt = $formulaire->exportValue('alt');
        $feuille->image_alt = $formulaire->exportValue('image_alt');
        $feuille->position = $formulaire->exportValue('position');
        $date = $formulaire->exportValue('date');
        $feuille->date = mktime(0, 0, 0, (int) $date['M'], (int) $date['d'], (int) $date['Y']);
        $feuille->etat = $formulaire->exportValue('etat');
        $feuille->patterns = $formulaire->exportValue('patterns');

        if ($action == 'ajouter') {
            if ($feuille->inserer()) {
                Logs::log('Ajout de la feuille ' . $formulaire->exportValue('nom'));
                afficherMessage('La feuille a été ' . (($action === 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=site_feuilles&action=lister');
            } else {
                $smarty->assign('erreur', 'Une erreur est survenue lors de l\'ajout de la feuille');
            }
        } elseif ($feuille->modifier()) {
            Logs::log('Ajout de la feuille ' . $formulaire->exportValue('nom'));
            afficherMessage('La feuille a été ' . (($action == 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=site_feuilles&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de la modification de la feuille');
        }
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));
}

function process($values): void
{
    global $file;
    if ($file->isUploadedFile()) {
        $file->moveUploadedFile($path);
    } else {
        print "No file uploaded";
    }
}
