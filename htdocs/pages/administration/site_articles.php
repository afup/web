<?php

declare(strict_types=1);

// Impossible to access the file itself
use Afup\Site\Corporate\Article;
use Afup\Site\Corporate\Articles;
use Afup\Site\Corporate\Rubriques;
use Afup\Site\Forum\Forum;
use Afup\Site\Utils\Logs;
use AppBundle\Controller\LegacyController;

/** @var LegacyController $this */
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$userRepository = $this->userRepository;

$action = verifierAction(['lister', 'ajouter', 'modifier', 'supprimer']);
$tris_valides = ['titre', 'date'];
$sens_valides = ['asc', 'desc'];
$smarty->assign('action', $action);




$articles = new Articles($bdd);

$forum  = new Forum($bdd);
$forumLabelsById = [];
foreach ($forum->obtenirListe(null, '*', 'date_debut DESC') as $forum) {
    $forumLabelsById[$forum['id']] = $forum['titre'];
}

function checkNoSpace($value): bool
{
    return !preg_match('/(\s)/', $value);
}

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

    $articlesList = [];
    foreach ($articles->obtenirListe($list_champs, $list_ordre . ' ' . $list_sens, $list_filtre) as $article) {
        $article['theme_label'] = Article::getThemeLabel($article['theme']);
        $article['forum_label'] = $forumLabelsById[$article['id_forum']] ?? '';
        $articlesList[] = $article;
    }

    // Mise en place de la liste dans le scope de smarty
    $smarty->assign('articles', $articlesList);
} elseif ($action == 'supprimer') {
    $article = new Article($_GET['id']);
    if ($article->supprimer()) {
        Logs::log('Suppression de l\'article ' . $_GET['id']);
        afficherMessage('L\'article a été supprimé', 'index.php?page=site_articles&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de l\'article', 'index.php?page=site_articles&action=lister', true);
    }
} else {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $article = new Article($id);
    $rubriques = new Rubriques();
    $users = [null => ''];
    foreach ($userRepository->search() as $user) {
        $users[$user->getId()] = $user->getFirstName() . ' ' . $user->getLastName();
    }

    $formulaire = instancierFormulaire();
    if ($action == 'ajouter') {
        $formulaire->setDefaults(['date' => time(),
                                       'position' => 0,
                                       'id_personne_physique' => $droits->obtenirIdentifiant(),
                                       'type_contenu' => Article::TYPE_CONTENU_MARKDOWN,
                                       'etat' => 0]);
    } else {
        $champs = $article->charger();
        $formulaire->setDefaults($article->exportable());
    }

    $formulaire->addElement('header'  , ''                         , 'Article');

    $abstractClass = 'simplemde';
    if ($action != 'ajouter' && false === $article->isTypeContenuMarkdown()) {
        $abstractClass = 'tinymce';
    }

    $formulaire->addElement('text'    , 'titre'                    , 'Titre'           , ['size' => 60, 'maxlength' => 255]);
    $formulaire->addElement('textarea', 'chapeau'                  , 'Chapeau'         , ['cols' => 42, 'rows'      => 10, 'class' => $abstractClass]);
    $formulaire->addElement('textarea', 'contenu'                  , 'Contenu'         , ['cols' => 42, 'rows'      => 20, 'class'=> $abstractClass]);
    $formulaire->addElement('hidden', 'type_contenu');

    $formulaire->addElement('header'  , ''                         , 'M&eacute;ta-donn&eacute;es');
    $formulaire->addElement('text'    , 'raccourci'                , 'Raccourci'      , ['size' => 60, 'maxlength' => 255]);
    $formulaire->addElement('select'  , 'id_site_rubrique'         , 'Rubrique'       , [null => '' ] + $rubriques->obtenirListe('id, nom', 'nom', null, true));
    $formulaire->addElement('select'  , 'id_personne_physique'     , 'Auteur'         , $users);
    $formulaire->addElement('date'    , 'date'                     , 'Date'           , ['language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 1]);
    $formulaire->addElement('select'  , 'position'                 , 'Position'       , $article->positionable());
    $formulaire->addElement('select'  , 'etat'                     , 'Etat'           , [-1 => 'Hors ligne', 0 => 'En attente', 1 => 'En ligne']);
    $formulaire->addElement('select'  , 'theme'                    , 'Thème'          , ['' => ''] + Article::getThemesLabels());
    $formulaire->addElement('select'  , 'id_forum'                , 'Forum'          , ['' => ''] + $forumLabelsById);

    $formulaire->addElement('header'  , 'boutons'                  , '');
    $formulaire->addElement('submit'  , 'soumettre'                , ucfirst($action));

    $formulaire->addRule('titre'       , 'Titre manquant'       , 'required');
    $formulaire->addRule('contenu'     , 'Contenu manquant'     , 'required');
    $formulaire->addRule('raccourci'   , 'Raccourci manquant'   , 'required');
    $formulaire->addRule('id_site_rubrique'   , 'Rubrique manquante'   , 'required');

    $formulaire->registerRule('checkNoSpace', 'callback', 'checkNoSpace');
    $formulaire->addRule('raccourci', 'Ne doit pas contenir d\'espace', 'checkNoSpace', true);


    if ($formulaire->validate()) {
        $article->id_site_rubrique = $formulaire->exportValue('id_site_rubrique');
        $article->id_personne_physique = $formulaire->exportValue('id_personne_physique');
        $article->titre = $formulaire->exportValue('titre');
        $article->raccourci = $formulaire->exportValue('raccourci');
        $article->chapeau = $formulaire->exportValue('chapeau');
        $article->contenu = $formulaire->exportValue('contenu');
        $article->type_contenu = $formulaire->exportValue('type_contenu');
        $article->position = $formulaire->exportValue('position');
        $date = $formulaire->exportValue('date');

        $article->date = mktime((int) $date['H'],(int) $date['i'], (int) $date['s'], (int) $date['M'], (int) $date['d'], (int) $date['Y']);
        $article->etat = $formulaire->exportValue('etat');
        $article->theme = $formulaire->exportValue('theme');
        $article->id_forum = $formulaire->exportValue('id_forum');

        $ok = $action == 'ajouter' ? $article->inserer() : $article->modifier();

        if ($ok) {
            if ($action == 'ajouter') {
                Logs::log('Ajout de l\'article ' . $formulaire->exportValue('titre'));
            } else {
                Logs::log('Modification de l\'article ' . $formulaire->exportValue('titre') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('L\'article a été ' . (($action == 'ajouter') ? 'ajouté' : 'modifié'), 'index.php?page=site_articles&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'article');
        }
    }

    if ($action == 'modifier') {
        $smarty->assign('url_site', $this->urlGenerator->generate('news_display', ['code' => $article->getCode()]));
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));
}
