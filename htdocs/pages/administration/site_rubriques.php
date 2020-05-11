<?php

// Impossible to access the file itself
use Afup\Site\Corporate\Feuilles;
use Afup\Site\Corporate\Rubrique;
use Afup\Site\Corporate\Rubriques;
use Afup\Site\Utils\Logs;
use AppBundle\Association\Model\Repository\UserRepository;

/** @var \AppBundle\Controller\LegacyController $this */
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$userRepository = $this->get(UserRepository::class);

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer'));
$tris_valides = array('titre', 'date');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);




$rubriques = new Rubriques($bdd);
$feuilles = new Feuilles($bdd);

if ($action == 'lister') {
    $list_champs     = '*';
    $list_ordre      = 'date';
    $list_sens       = 'desc';
    $list_filtre     = false;

    if (isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
        $list_sens = $_GET['sens'];
    } else {
        $_GET['sens'] = $list_sens;
    }
    if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides)) {
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
    $smarty->assign('rubriques', $rubriques->obtenirListe($list_champs, $list_ordre.' '.$list_sens, $list_filtre, $list_filtre));

} elseif ($action == 'supprimer') {
    $rubrique = new Rubrique($_GET['id']);
    if ($rubrique->supprimer()) {
        Logs::log('Suppression de la rubrique ' . $_GET['id']);
        afficherMessage('La rubrique a été supprimée', 'index.php?page=site_rubriques&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de la rubrique', 'index.php?page=site_rubriques&action=lister', true);
    }

} else {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $rubrique = new Rubrique($id);
    $users = [null => ''];
    foreach ($userRepository->search() as $user) {
        $users[$user->getId()] = $user->getLastName().' '.$user->getFirstName();
    }

    $formulaire = instancierFormulaire();
    if ($action == 'ajouter') {
        $formulaire->setDefaults(array('date' => time(),
                                       'position' => 0,
                                       'id_personne_physique' => $droits->obtenirIdentifiant(),
                                       'etat' => 0));
    } else {
        $rubrique->charger();
        $formulaire->setDefaults($rubrique->exportable());
    }

    $formulaire->addElement('header'  , ''                         , 'rubrique');
    $formulaire->addElement('text'    , 'nom'                      , 'Nom'             , array('size' => 60, 'maxlength' => 255));
    $formulaire->addElement('textarea', 'descriptif'               , 'Descriptif'      , array('cols' => 42, 'rows'      => 10, 'class' => 'tinymce'));
    $formulaire->addElement('textarea', 'contenu'                  , 'Contenu'         , array('cols' => 42, 'rows'      => 20, 'class' => 'tinymce'));
    $formulaire->addElement('static'  , 'note'                     , '', 'Taille requise : 43 x 37 pixels');
    $formulaire->addElement('file'    , 'icone'                    , 'Icône');
    $formulaire->addElement('static'  , 'html'                     , '', '<img src="'.$conf->obtenir('web|path').'/templates/site/images/'.$rubrique->icone.'" /><br />');
    $formulaire->addElement('hidden'  , 'icone_default'            , $rubrique->icone);

    $formulaire->addElement('header'  , ''                         , 'Méta-données');
    $formulaire->addElement('text'    , 'raccourci'                , 'Raccourci'        , array('size' => 60, 'maxlength' => 255));
    $formulaire->addElement('select'  , 'id_parent'                , 'Parent'           , array(null => '' ) + $rubriques->obtenirListe('id, nom', 'nom', true));
    $formulaire->addElement('select'  , 'id_personne_physique'     , 'Auteur'           , $users);
    $formulaire->addElement('date'    , 'date'                     , 'Date'             , array('language' => 'fr', 'minYear' => 2001, 'maxYear' => date('Y')));
    $formulaire->addElement('select'  , 'position'                 , 'Position'         , $rubrique->positionable());
    $formulaire->addElement('select'  , 'etat'                     , 'Etat'             , array(-1 => 'Hors ligne', 0 => 'En attente', 1 => 'En ligne'));
    $formulaire->addElement('select'  , 'feuille_associee'         , 'Feuille associée'    , array(null => '' ) + $feuilles->obtenirListe('id, nom', 'nom', true));

    $formulaire->addElement('header'  , 'boutons'                  , '');
    $formulaire->addElement('submit'  , 'soumettre'                , ucfirst($action));

    $formulaire->addRule('nom'         , 'Nom manquant'         , 'required');
    $formulaire->addRule('contenu'     , 'Contenu manquant'     , 'required');
    $formulaire->addRule('raccourci'   , 'Raccourci manquant'   , 'required');

    if ($formulaire->validate()) {
        $file = $formulaire->getElement('icone');
        $data = $file->getValue();
        if ($data['name']) {
            $file->moveUploadedFile(dirname(__FILE__).'/../../templates/site/images/');
            $data = $file->getValue();
            $rubrique->icone = $data['name'];
        } else {
            $rubrique->icone = $formulaire->exportValue('icone_default');
        }

        $rubrique->id_parent = $formulaire->exportValue('id_parent');
        $rubrique->id_personne_physique = $formulaire->exportValue('id_personne_physique');
        $rubrique->nom = $formulaire->exportValue('nom');
        $rubrique->raccourci = $formulaire->exportValue('raccourci');
        $rubrique->descriptif = $formulaire->exportValue('descriptif');
        $rubrique->contenu = $formulaire->exportValue('contenu');
        $rubrique->position = $formulaire->exportValue('position');
        $date = $formulaire->exportValue('date');
        $rubrique->date = mktime(0, 0, 0, $date['M'], $date['d'], $date['Y']);
        $rubrique->etat = $formulaire->exportValue('etat');
        $rubrique->feuille_associee = $formulaire->exportValue('feuille_associee');

        if ($action == 'ajouter') {
            $ok = $rubrique->inserer();
        } else {
            $ok = $rubrique->modifier();
        }

        if ($ok) {
            if ($action == 'ajouter') {
                Logs::log('Ajout de la rubrique ' . $formulaire->exportValue('nom'));
            } else {
                Logs::log('Modification de la rubrique ' . $formulaire->exportValue('nom') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('La rubrique a été ' . (($action == 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=site_rubriques&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de la rubrique');
        }
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));
}
