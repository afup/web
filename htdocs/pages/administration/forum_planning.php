<?php

declare(strict_types=1);

// Impossible to access the file itself
use Afup\Site\Forum\AppelConferencier;
use Afup\Site\Forum\Forum;
use Afup\Site\Utils\Logs;
use Afup\Site\Utils\Pays;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(['lister', 'ajouter', 'modifier', 'commenter', 'supprimer', 'voter']);
$tris_valides = [];
$sens_valides = ['asc' , 'desc'];
$smarty->assign('action', $action);





$forum = new Forum($bdd);
$forum_appel = new AppelConferencier($bdd);

if ($action == 'lister') {
    $list_champs = 's.*';
    $list_ordre = 's.date_soumission';
    $list_sens = 'desc';
    $list_associatif = false;
    $list_filtre = false;


    if (!isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0) {
        $_GET['id_forum'] = $forum->obtenirDernier();
    }

    $rs_forum = $forum->obtenir($_GET['id_forum']);
    $annee_forum = $rs_forum['forum_annee'];

    $sessions = $forum_appel->obtenirListeSessionsPlannifies($_GET['id_forum']);
    $salles = $forum_appel->obtenirListeSalles($_GET['id_forum'], true);

    $smarty->assign('agenda', $forum->genAgenda($annee_forum, true, false, $_GET['id_forum']));
    $smarty->assign('id_forum', $_GET['id_forum']);
    $smarty->assign('forums', $forum->obtenirListe());
    $smarty->assign('sessions', $sessions);
} elseif ($action == 'supprimer') {
    if ($forum_appel->supprimerSessionDuPlanning($_GET['id'])) {
        Logs::log('Suppression de la programmation de la session ' . $_GET['id']);
        afficherMessage('La programmation de la session a été supprimée', 'index.php?page=forum_planning&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de la session', 'index.php?page=forum_planning&action=lister', true);
    }
} else {
    $pays = new Pays($bdd);
    $formulaire = instancierFormulaire();

    $champs = $forum_appel->obtenirPlanningDeSession($_GET['id_session']);
    $conferenciers = $forum_appel->obtenirConferenciersPourSession($_GET['id_session']);

    if (empty($champs['debut']) && empty($champs['fin'])) {
        $id_forum = $forum->obtenirDernier();
        $forum_donnees = $forum->obtenir($id_forum);
        $champs['debut'] = $forum_donnees['date_debut'];
        $champs['fin'] = $forum_donnees['date_debut'];
    }

    $formulaire->setDefaults($champs);
    $id = $_GET['id'] ?? 0;

    $formulaire->addElement('hidden', 'id'	  , null);
    $formulaire->addElement('hidden', 'id_session', $_GET['id_session']);
    $formulaire->addElement('hidden', 'id_forum', $champs['id_forum']);

    $formulaire->addElement('header', null, 'Présentation');
    $formulaire->addElement('static', 'titre'   , 'Titre' , '<strong>' . $champs['titre'] . '</strong>');
    $formulaire->addElement('static', 'abstract', 'Résumé', $champs['abstract']);

    foreach ($conferenciers as $conferencier) {
        $formulaire->addElement('static', 'conferencier_id_' . $conferencier['conferencier_id'], 'Conférencier', $conferencier['nom'] . ' ' . $conferencier['prenom'] . ' (' . $conferencier['societe'] . ')');
    }

    $formulaire->addElement('header', null, 'Plannification');
    $formulaire->addElement('date'	, 'debut'   , 'Début', ['language' => 'fr', 'format' => "dMY H:i", 'minYear' => date('Y'), 'maxYear' => date('Y') + 1, 'minHour' => 8, 'maxHour' => 18, 'optionIncrement' => ['i' => 5]]);
    $formulaire->addElement('date'	, 'fin'	 , 'Fin'  , ['language' => 'fr', 'format' => "dMY H:i", 'minYear' => date('Y'), 'maxYear' => date('Y') + 1, 'optionIncrement' => ['i' => 5], 'minHour' => 8, 'maxHour' => 18]);
    $formulaire->addElement('select'  , 'id_salle', 'Salle', [null => '' ] + $forum_appel->obtenirListeSalles($champs['id_forum'], true));
    $formulaire->addElement('text'    , 'joindin'          , 'Id de la conférence chez joind.in' , ['size' => 40, 'maxlength' => 10]);

    $formulaire->addElement('header', 'boutons'  , '');
    $formulaire->addElement('submit', 'soumettre', 'Soumettre');

    $formulaire->addRule('debut'   , 'Date et heure du début manquants', 'required');
    $formulaire->addRule('fin'	 , 'Date et heure de fin manquants'  , 'required');
    $formulaire->addRule('id_salle', 'Nom de la salle manquant'		, 'required');

    if ($formulaire->validate()) {
        $valeurs = $formulaire->exportValues();

        if ($id == 0) {
            $planning_id = $forum_appel->ajouterSessionDansPlanning($valeurs['id_forum'],
                $valeurs['id_session'],
                mktime((int) $valeurs['debut']['H'], (int) $valeurs['debut']['i'], 0, (int) $valeurs['debut']['M'], (int) $valeurs['debut']['d'], (int) $valeurs['debut']['Y']),
                mktime((int) $valeurs['fin']['H'], (int) $valeurs['fin']['i'], 0, (int) $valeurs['fin']['M'], (int) $valeurs['fin']['d'], (int) $valeurs['fin']['Y']),
                $valeurs['id_salle']);

            $ok = (bool) $planning_id;
        } else {
            $planning_id = (int) $_GET['id'];
            $ok = $forum_appel->modifierSessionDuPlanning($planning_id,
                $valeurs['id_forum'],
                $valeurs['id_session'],
                mktime((int) $valeurs['debut']['H'], (int) $valeurs['debut']['i'], 0, (int) $valeurs['debut']['M'], (int) $valeurs['debut']['d'], (int) $valeurs['debut']['Y']),
                mktime((int) $valeurs['fin']['H'],(int) $valeurs['fin']['i'], 0,(int) $valeurs['fin']['M'],(int) $valeurs['fin']['d'], (int) $valeurs['fin']['Y']),
                $valeurs['id_salle']);
            $forum_appel->modifierJoindinSession($valeurs['id_session'], $valeurs['joindin']);
        }

        if ($ok) {
            if ($action == 'ajouter') {
                Logs::log('Ajout du planning de la session de ' . $formulaire->exportValue('titre'));
            } else {
                Logs::log('Modification du planning de la session de ' . $formulaire->exportValue('titre') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('Le planning de la session a été ' . (($action == 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=forum_planning&action=lister&id_forum=' . $valeurs['id_forum']);
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' du planning de la session');
        }
    }

    $current = $forum->obtenir($champs['id_forum'], 'titre');
    $smarty->assign('forum_name', $current['titre']);
    $smarty->assign('formulaire', genererFormulaire($formulaire));
}
