<?php

declare(strict_types=1);

// Impossible to access the file itself
use Afup\Site\Forum\AppelConferencier;
use Afup\Site\Forum\Forum;
use Afup\Site\Utils\Logs;
use Afup\Site\Utils\Pays;
use AppBundle\Controller\LegacyController;
use AppBundle\Event\Model\Talk;
use Assert\Assertion;

/** @var LegacyController $this */
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(['lister', 'ajouter', 'modifier', 'supprimer']);
$tris_valides = ['s.titre', 's.date_soumission'];
$sens_valides = ['asc' , 'desc'];
$smarty->assign('action', $action);

$eventRepository = $this->eventRepository;
$speakerRepository = $this->speakerRepository;
$talkRepository = $this->talkRepository;

$forum = new Forum($bdd);
$forum_appel = new AppelConferencier($bdd);

if ($action == 'lister') {
    // Valeurs par défaut des paramètres de tri
    $list_champs = 's.*';
    $list_ordre = 's.date_soumission';
    $list_sens = 'desc';
    $list_associatif = false;
    $list_filtre = false;
    $list_type = 'session';


    // Modification des paramètres de tri en fonction des demandes passées en GET
    if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides)
        && isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
        $list_ordre = $_GET['tri'] . ' ' . $_GET['sens'];
    }
    if (isset($_GET['filtre'])) {
        $list_filtre = $_GET['filtre'];
    }
    $needsMentoring = isset($_GET['filtre_needs_mentoring']) && $_GET['filtre_needs_mentoring'] == '1' ? true : null;
    $planned = isset($_GET['filtre_planned']) && $_GET['filtre_planned'] == '1' ? true : null;
    if (isset($_GET['type'])) {
        $list_type = $_GET['type'];
    }

    if (!isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0) {
        $_GET['id_forum'] = $forum->obtenirDernier();
    }

    $smarty->assign('id_forum', $_GET['id_forum']);
    $smarty->assign('list_type', $list_type);

    $smarty->assign('forums', $forum->obtenirListe());

    $listeSessions = $forum_appel->obtenirListeSessions($_GET['id_forum'], $list_champs, $list_ordre, $list_associatif, $list_filtre,$list_type, $needsMentoring, $planned);
    $moi = $droits->obtenirIdentifiant();
    $votant = in_array($_SESSION['afup_login'] ?? '', []);
    $maxVotant = count([]);
    foreach ($listeSessions as &$session) {
        $session['conferencier'] = $forum_appel->obtenirConferenciersPourSession($session['session_id']);
        $session['commentaires'] = $forum_appel->obtenirCommentairesPourSession($session['session_id']);
        $session['jai_commente'] = false;
        if ($votant) {
            $session['jai_vote'] = $forum_appel->dejaVote($moi, $session['session_id']);
        }
        foreach ($session['commentaires'] as $c) {
            if ($c['id_personne_physique'] == $moi) {
                $session['jai_commente'] = true;
            }
        }
        if ($votant) {
            $session['nb_vote'] = $forum_appel->nbVoteSession($session['session_id']);
        }

        $session['joindin_url'] = null;

        if (isset($session['joindin']) && $session['joindin'] > 0) {
            $talk = new Talk();
            $talk->setTitle($session['titre']);
            $talk->setId((int) $session['session_id']);
            $talk->setJoindinId((int) $session['joindin']);
            $session['joindin_url'] = $talk->getJoindinUrl();
        }
    }
    $smarty->assign('sessions', $listeSessions);
    $smarty->assign('votant', $votant);
    $smarty->assign('nb_votant', $maxVotant);
} elseif ($action == 'supprimer') {
    if ($forum_appel->supprimerSession($_GET['id'])) {
        Logs::log('Suppression de la session ' . $_GET['id']);
        afficherMessage('La session a été supprimée', 'index.php?page=forum_sessions&action=lister&type=' . $list_type);
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de la session', 'index.php?page=forum_sessions&action=lister&type=' . $list_type, true);
    }
} else {
    $pays = new Pays($bdd);

    $talk = null;

    $formulaire = instancierFormulaire();
    if ($action != 'ajouter') {
        $champs = $forum_appel->obtenirSession($_GET['id']);

        $talk = $talkRepository->get($_GET['id']);

        $formulaire->setDefaults($champs);

        if (isset($champs) && isset($champs['id_forum'])) {
            $_GET['id_forum'] = $champs['id_forum'];
        }
    }

    $id = $_GET['id'] ?? 0;
    $formulaire->addElement('hidden', 'id'      , $id);
    $formulaire->addElement('hidden', 'id_forum', $_GET['id_forum']);

    $formulaire->addElement('header', null, 'Présentation');

    $formulaire->addElement('date'    , 'date_soumission', 'Soumission', ['language' => 'fr', 'minYear' => date('Y') -5, 'maxYear' => date('Y') +5]);
    $formulaire->addElement('text'    , 'titre'          , 'Titre' , ['size' => 40, 'maxlength' => 150]);

    $abstractClass = 'simplemde';
    $useMarkdown = true;
    if ($talk !== null && $talk->getUseMarkdown() === false) {
        $useMarkdown = false;
        $abstractClass = 'tinymce';
    }

    $formulaire->addElement('textarea', 'abstract'       , 'Résumé', ['cols' => 40, 'rows' => 15,'class'=> $abstractClass]);
    $formulaire->addElement('hidden', 'use_markdown', (int) $useMarkdown);


    $typesLabelsByKey = Talk::getTypeLabelsByKey();
    asort($typesLabelsByKey);
    $groupe = [];
    foreach ($typesLabelsByKey as $genreKey => $genreLabel) {
        $groupe[] = $formulaire->createElement('radio', 'genre', null, $genreLabel, $genreKey);
    }
    $formulaire->addGroup($groupe, 'groupe_type_pres', "Type de session", '<br />', false);

    $groupe = [];
    $groupe[] = $formulaire->createElement('radio', 'plannifie', null, 'Oui', 1);
    $groupe[] = $formulaire->createElement('radio', 'plannifie', null, 'Non', 0);
    $formulaire->addGroup($groupe, 'groupe_plannifie', "Plannifi&eacute;", '<br />', false);

    $groupe = [];

    $groupe[] = $formulaire->createElement('radio', 'skill', null, 'N/A', Talk::SKILL_NA);
    $groupe[] = $formulaire->createElement('radio', 'skill', null, 'Junior', Talk::SKILL_JUNIOR);
    $groupe[] = $formulaire->createElement('radio', 'skill', null, 'Medior', Talk::SKILL_MEDIOR);
    $groupe[] = $formulaire->createElement('radio', 'skill', null, 'Senior', Talk::SKILL_SENIOR);
    $formulaire->addGroup($groupe, 'groupe_skill', "Niveau", '<br />', false);

    $formulaire->addElement('checkbox'    , 'needs_mentoring'          , "Demande a bénéficier du programme d'accompagnement des jeunes speakers");
    $formulaire->addElement('checkbox', 'with_workshop', "Propose un atelier");
    $formulaire->addElement('textarea', 'workshop_abstract', 'Résumé de l\'atelier', ['cols' => 40, 'rows' => 15]);


    if ($action != 'ajouter') {
        $formulaire->addElement('text'    , 'joindin'          , 'Id de la conférence chez joind.in' , ['size' => 40, 'maxlength' => 10]);
        $formulaire->addElement('text'    , 'youtube_id'          , 'Id de la conférence sur youtube' , ['size' => 40, 'maxlength' => 30]);
        $formulaire->addElement('text'    , 'slides_url'          , 'URL où trouver les slides' , ['size' => 80, 'maxlength' => 255]);
        $formulaire->addElement('text'    , 'openfeedback_path'          , 'Chemin la conférence sur openfeedback' , ['size' => 80, 'maxlength' => 255]);
        $formulaire->addElement('text'    , 'blog_post_url'          , 'URL de la version  article de blog de la conférence' , ['size' => 80, 'maxlength' => 255]);
        $formulaire->addElement('text'    , 'interview_url'          , "URL de l'interview" , ['size' => 80, 'maxlength' => 255]);
        $formulaire->addElement('select', 'language_code', 'Langue', Talk::getLanguageLabelsByKey());

        $formulaire->addElement('checkbox'    , 'video_has_fr_subtitles'          , "Sous titres FR présents");
        $formulaire->addElement('checkbox'    , 'video_has_en_subtitles'          , "Sous titres EN présents");
        $formulaire->addElement('date'  , 'date_publication'       , 'Date de publication'               , ['language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5]);
        $formulaire->addElement('textarea'    , 'tweets'          , "Tweets", ['style' => "width:100%;min-height:100px"]);
        $formulaire->addElement('textarea'    , 'transcript'          , "Sous titres en français (format SRT)", ['style' => "width:100%;min-height:100px"]);
        $formulaire->addElement('textarea', 'verbatim', 'Verbatim', ['cols' => 40, 'rows' => 15,'class'=> 'simplemde']);
    }


    $formulaire->addElement('header', null, 'Conférencier(s)');
    $event = $eventRepository->get($_GET['id_forum']);
    Assertion::notNull($event);
    $conferenciers = [null => ''];
    foreach ($speakerRepository->searchSpeakers($event) as $speaker) {
        $conferenciers[$speaker->getId()] = $speaker->getLastname() . ' ' . $speaker->getFirstname();
    }
    $formulaire->addElement('select', 'conferencier_id_1'    , 'N°1', $conferenciers);
    $formulaire->addElement('select', 'conferencier_id_2'    , 'N°2', $conferenciers);
    $formulaire->addElement('select', 'conferencier_id_3'    , 'N°3', $conferenciers);

    if ($action != 'ajouter') {
        $conferenciers = $forum_appel->obtenirConferenciersPourSession($id);
        $smarty->assign('session_conferenciers', $conferenciers);
    }

    $commentaires = $forum_appel->obtenirCommentairesPourSession($id);
    if (is_array($commentaires) && count($commentaires)) {
        $formulaire->addElement('header', null, 'Commentaires');
        $feed = '<div class="ui feed">';
        foreach ($commentaires as $commentaire) {
            $feed .= '<div class="event">';
            $feed .= '<div class="content">';
            $feed .= '<div class="summary">';
            $feed .= $commentaire['nom'] . ' ' . $commentaire['prenom'];
            $feed .= '<div class="date">';
            $feed .= date('d/m/Y h:i', $commentaire['date']);
            $feed .= '</div>';
            $feed .= '</div>';
            $feed .= '<div class="extra text">';
            $feed .= $commentaire['commentaire'];
            $feed .= '</div>';
            $feed .= '</div>';
            $feed .= '</div>';
        }
        $feed .= '</div>';
        $formulaire->addElement('static', 'note', '', $feed);
    }

    $formulaire->addElement('header', 'boutons'  , '');
    $formulaire->addElement('submit', 'soumettre', 'Soumettre');

    // On ajoute les règles
    $formulaire->addRule('titre'            , 'Titre manquant'             , 'required');
    $formulaire->addRule('conferencier_id_1', 'Conférencier n°1 manquant'          , 'required');

    if ($formulaire->validate()) {
        $valeurs = $formulaire->exportValues();
        $valeurs += ['needs_mentoring' => 0];

        if ($action == 'ajouter') {
            $session_id = $forum_appel->ajouterSession(
                $valeurs['id_forum'],
                $valeurs['date_soumission']['Y'] . '-' . $valeurs['date_soumission']['M'] . '-' . $valeurs['date_soumission']['d'],
                $valeurs['titre'],
                $valeurs['abstract'],
                (int) $valeurs['genre'],
                (int) $valeurs['plannifie'],
                isset($valeurs['needs_mentoring']) ? (int) $valeurs['needs_mentoring'] : 0,
                (int) $valeurs['skill'],
                $valeurs['use_markdown']
            );
            $ok = (bool) $session_id;
        } else {
            $session_id = (int) $_GET['id'];
            $ok = $forum_appel->modifierSession($session_id,
                                                $valeurs['id_forum'],
                                                $valeurs['date_soumission']['Y'] . '-' . $valeurs['date_soumission']['M'] . '-' . $valeurs['date_soumission']['d'],
                                                $valeurs['titre'],
                                                $valeurs['abstract'],
                                                (int) $valeurs['genre'],
                                                (int) $valeurs['plannifie'],
                                                $valeurs['joindin'],
                                                $valeurs['youtube_id'],
                                                $valeurs['slides_url'],
                                                $valeurs['openfeedback_path'],
                                                $valeurs['blog_post_url'],
                                                $valeurs['interview_url'],
                                                $valeurs['language_code'],
                                                (int) $valeurs['skill'],
                                                (int) $valeurs['needs_mentoring'],
                                                $valeurs['use_markdown'],
                                                $valeurs['video_has_fr_subtitles'],
                                                $valeurs['video_has_en_subtitles'],
                                                $valeurs['date_publication']['Y'] . '-' . $valeurs['date_publication']['M'] . '-' . $valeurs['date_publication']['d'] . ' ' . $valeurs['date_publication']['H'] . ':' . $valeurs['date_publication']['i'] . ':' . $valeurs['date_publication']['s'],
                                                $valeurs['tweets'],
                                                $valeurs['transcript'],
                                                $valeurs['verbatim']
            );
            $forum_appel->delierSession($session_id);
        }

        if ($ok) {
            $ok &= $forum_appel->lierConferencierSession($valeurs['conferencier_id_1'], $session_id);
            $ok &= $forum_appel->lierConferencierSession($valeurs['conferencier_id_2'], $session_id);
            $ok &= $forum_appel->lierConferencierSession($valeurs['conferencier_id_3'], $session_id);
        }

        if ($ok) {
            if ($action == 'ajouter') {
                Logs::log('Ajout de la session de ' . $formulaire->exportValue('titre'));
            } else {
                Logs::log('Modification de la session de ' . $formulaire->exportValue('titre') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('La session a été ' . (($action == 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=forum_sessions&action=lister&id_forum=' . $valeurs['id_forum']);
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de la session');
        }
    }

    $current = $forum->obtenir($_GET['id_forum'], 'titre');
    $smarty->assign('forum_name', $current['titre']);
    $smarty->assign('formulaire', genererFormulaire($formulaire));
    $smarty->assign('talk', $talk);
}
