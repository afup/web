<?php

declare(strict_types=1);

// Impossible to access the file itself
use Afup\Site\Forum\Forum;
use Afup\Site\Forum\Partenaires;
use Afup\Site\Niveau_Partenariat;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(['lister', 'ajouter', 'modifier', 'supprimer']);
$smarty->assign('action', $action);




$partenaires = new Partenaires($bdd);
$forums = new Forum($bdd);
$niveauPartenariat = new Niveau_Partenariat($bdd);

if ($action == 'lister') {
    // Mise en place de la liste dans le scope de smarty
    $sponsors = $partenaires->obtenirListe();
    $smarty->assign('sponsors', $sponsors);
} elseif ($action == 'supprimer') {
    if ($partenaires->supprimer($_GET['id'])) {
        Logs::log('Suppression du partenaire ' . $_GET['id']);
        afficherMessage('Le partenaire a été supprimé', 'index.php?page=forum_partenaire&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression du partenaire', 'index.php?page=forum_partenaire&action=lister', true);
    }
} else {
    $formulaire = instancierFormulaire();
    if ($action == 'ajouter') {
        $formulaire->setDefaults(['ranking' => 1]);
    } else {
        $champs = $partenaires->obtenir($_GET['id']);
        $forum = $forums->obtenir($champs['id_forum']);

        $formulaire->setDefaults($champs);

        if (isset($champs) && isset($champs['id'])) {
            $_GET['id'] = $champs['id'];
        }

        $formulaire->addElement('hidden', 'id', $_GET['id']);
    }

    $formulaire->addElement('header'  , ''            , 'Partenaire de forum');
    $formulaire->addElement('select'  , 'id_forum'    , 'Forum'          , $forums->obtenirListe(null,'id, titre', 'titre', true));
    $formulaire->addElement('select'  , 'id_niveau_partenariat' , 'Partenariat' , $niveauPartenariat->obtenirListe());
    $formulaire->addElement('text'    , 'ranking'     , 'Rang'           , ['size' => 30, 'maxlength' => 40]);
    $formulaire->addElement('text'    , 'nom'         , 'Nom'            , ['size' => 30, 'maxlength' => 100]);
    $formulaire->addElement('textarea', 'presentation', 'Présentation'   , ['cols' => 42, 'rows'      => 15, 'class' => 'tinymce']);
    $formulaire->addElement('text'    , 'site'        , 'Site'           , ['size' => 30]);
    $formulaire->addElement('static'  , 'note'                           , '', 'Faire attention à la taille');
    $formulaire->addElement('file'    , 'logo'        , 'Logo');
    if ($action == 'modifier') {
        $formulaire->addElement('static'  , 'html'                     , '', '<img src="/templates/' . $forum['path'] . '/images/' . $champs['logo'] . '" /><br />');
        $chemin = realpath('../../templates/' . $forum['path'] . '/images/' . $champs['logo']);
        if ($champs['logo'] && $chemin && file_exists($chemin)) {
            if ((function_exists('getimagesize'))) {
                $info = getimagesize($chemin);
                $formulaire->addElement('static'  , 'html'                     , '', 'Taille actuelle : ' . $info[3]);
                $formulaire->addElement('static'  , 'html'                     , '', 'Type MIME : ' . $info['mime']);
            } else {
                $formulaire->addElement('static'  , 'html'                     , '', 'L\'extension GD n\'est pas présente sur ce serveur');
            }
        }
        $formulaire->addElement('hidden'  , 'logo_default'            , $champs['logo']);
    } else {
        $formulaire->addElement('hidden'  , 'logo_default'            , null);
    }
    $formulaire->addElement('submit'  , 'soumettre'   , 'Soumettre');

    $formulaire->addRule('id_forum' , 'Forum manquant' , 'required');
    $formulaire->addRule('id_niveau_partenariat' , 'Partenariat' , 'required');
    $formulaire->addRule('rang' , 'Rang manquant' , 'required');
    $formulaire->addRule('nom' , 'Nom manquant' , 'required');

    if ($formulaire->validate()) {
        $valeurs = $formulaire->exportValues();
        $forum = $forums->obtenir($valeurs['id_forum']);
        $file = $formulaire->getElement('logo');
        $data = $file->getValue();
        if ($data['name']) {
            $file->moveUploadedFile(realpath('../../templates/' . $forum['path'] . '/images/'));
            $data = $file->getValue();
            $valeurs['logo'] = $data['name'];
        } else {
            $valeurs['logo'] = $formulaire->exportValue('logo_default');
        }

        if ($action == 'ajouter') {
            $ok = $partenaires->ajouter($valeurs['id_forum'],
                                        $valeurs['id_niveau_partenariat'],
                                        $valeurs['ranking'],
                                        $valeurs['nom'],
                                        $valeurs['presentation'],
                                        $valeurs['site'],
                                        $valeurs['logo']);
        } else {
            $ok = $partenaires->modifier($_GET['id'],
                                         $valeurs['id_forum'],
                                         $valeurs['id_niveau_partenariat'],
                                         $valeurs['ranking'],
                                         $valeurs['nom'],
                                         $valeurs['presentation'],
                                         $valeurs['site'],
                                         $valeurs['logo']);
        }

        if ($ok) {
            if ($action == 'ajouter') {
                Logs::log('Ajout du partenaire de ' . $formulaire->exportValue('nom'));
            } else {
                Logs::log('Modification du partenaire de ' . $formulaire->exportValue('nom') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('Le partenaire a été ' . (($action == 'ajouter') ? 'ajouté' : 'modifié'), 'index.php?page=forum_partenaire&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' du partenaire');
        }
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));
}
