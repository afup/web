<?php

// Impossible to access the file itself
use Afup\Site\Forum\AppelConferencier;
use Afup\Site\Forum\Forum;
use Afup\Site\Utils\Logs;
use Afup\Site\Utils\Pays;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'commenter', 'supprimer', 'voter'));
$tris_valides = array('s.titre', 's.date_soumission');
$sens_valides = array('asc' , 'desc');
$smarty->assign('action', $action);





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
    $votant = in_array($_SESSION['afup_login'], $conf->obtenir('bureau'));
    $maxVotant = count($conf->obtenir('bureau'));
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
    }
    $smarty->assign('sessions', $listeSessions);
    $smarty->assign('votant', $votant);
    $smarty->assign('nb_votant', $maxVotant);
} elseif ($action == 'supprimer') {
    if ($forum_appel->supprimerSession($_GET['id'])) {
        Logs::log('Suppression de la session ' . $_GET['id']);
        afficherMessage('La session a été supprimée', 'index.php?page=forum_sessions&action=lister&type='.$list_type);
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de la session', 'index.php?page=forum_sessions&action=lister&type='.$list_type, true);
    }

} elseif ($action == 'commenter') {
    $genres = \AppBundle\Event\Model\Talk::getTypeLabelsByKey();

    $formulaire = instancierFormulaire();
    $id = isset($_GET['id']) ? $_GET['id'] : 0;
    $id_forum = isset($_GET['id_forum']) ? $_GET['id_forum'] : $forum->obtenirDernier();
    $formulaire->addElement('hidden', 'id'      , $id);

    $champs = $forum_appel->obtenirSession($_GET['id']);
    $conferenciers = $forum_appel->obtenirConferenciersPourSession($_GET['id']);
    $formulaire->addElement('header', null, 'Présentation');
    $formulaire->addElement('static', 'titre'            , 'Titre' , '<strong>'.$champs['titre'].'</strong>');
    $formulaire->addElement('static', 'abstract'         , 'Résumé', $champs['abstract']);
    foreach ($conferenciers as $conferencier) {
        $url = 'index.php?page=forum_conferenciers&action=modifier&id=' . $conferencier['conferencier_id'] . '&id_forum=' . $id_forum;
    	$formulaire->addElement('static',
            'conferencier_id_'.$conferencier['conferencier_id'],
            'Conférencier',
            '<a href="'.$url.'">'.$conferencier['nom'].' '.$conferencier['prenom'].'</a> ('.$conferencier['societe'].')'
        );
    }
    $formulaire->addElement('static', 'date_soumission', 'Soumission'     , $champs['date_soumission']);
    $formulaire->addElement('static', 'genre'          , 'Type de session', $genres[$champs['genre']]);

    $formulaire->addElement('header', null, 'Commentaires');
    $commentaires = $forum_appel->obtenirCommentairesPourSession($_GET['id']);
    if (is_array($commentaires)) {
	    foreach ($commentaires as $commentaire) {
	        $formulaire->addElement('static',
	        						'id_commentaire_'.$commentaire['id'],
	                                date('d/m/Y h:i', $commentaire['date']),
	                                $commentaire['commentaire'].'<br /><br /><em>'.$commentaire['nom'].' '.$commentaire['prenom'].'</em>');
	    }
    }


    $conf = $GLOBALS['AFUP_CONF'];

    $formulaire->addElement('header', null, 'Nouveau commentaire');
    $formulaire->addElement('textarea', 'commentaire', 'Commentaire', array('cols' => 40, 'rows' => 15,'class'=>'tinymce'));

    $formulaire->addElement('header', 'boutons'  , '');
	$formulaire->addElement('submit', 'soumettre', 'Soumettre');
	$formulaire->addElement('submit', 'passer'   , 'Passer');

	if (isset($_POST['passer'])) {
	    $url = 'index.php?page=forum_sessions&action=lister';
	    if ($id_next = $forum_appel->obtenirSessionSuivanteSansCommentaire($id_forum, $droits->obtenirIdentifiant())) {
            $url = 'index.php?page=forum_sessions&action=commenter&id=' . $id_next . '&id_forum=' . $id_forum;
        }
        afficherMessage('Direction une autre session sans commentaire', $url);

	} elseif ($formulaire->validate()) {
        $identifiant = $droits->obtenirIdentifiant();
		$valeurs = $formulaire->exportValues();

        if (!empty($valeurs['commentaire'])) {
	        $ok = $forum_appel->ajouterCommentaire($id,
				                                   $identifiant,
	            								   $valeurs['commentaire'],
				                                   time(),
				                                   0);

	        if ($ok) {
	            Logs::log('Ajout d\'un commentaire sur la session n°' . $formulaire->exportValue('id'));
	            $url = 'index.php?page=forum_sessions&action=lister';
	            if ($id_next = $forum_appel->obtenirSessionSuivanteSansCommentaire($id_forum, $droits->obtenirIdentifiant())) {
	                $url = 'index.php?page=forum_sessions&action=commenter&id=' . $id_next . '&id_forum=' . $id_forum;
	            }
	            afficherMessage('Un commentaire sur la session n°' . $formulaire->exportValue('id'). ' a été ajouté', $url);
	        } else {
	            $smarty->assign('erreur', 'Une erreur est survenue lors de l\'ajout du commentaire sur la session');
	        }
	    }

    }

    $current = $forum->obtenir($_GET['id_forum'], 'titre');
    $smarty->assign('forum_name', $current['titre']);
    $smarty->assign('formulaire', genererFormulaire($formulaire));

} elseif ($action == 'voter') {

    $genres = \AppBundle\Event\Model\Talk::getTypeLabelsByKey();

    $formulaire = instancierFormulaire();
    $id = isset($_GET['id']) ? $_GET['id'] : 0;
    $id_forum = isset($_GET['id_forum']) ? $_GET['id_forum'] : $forum->obtenirDernier();
    $formulaire->addElement('hidden', 'id'      , $id);

    $champs = $forum_appel->obtenirSession($_GET['id']);
    $conferenciers = $forum_appel->obtenirConferenciersPourSession($_GET['id']);
    $formulaire->addElement('header', null, 'Présentation');
    $formulaire->addElement('static', 'titre'            , 'Titre' , '<strong>'.$champs['titre'].'</strong>');
    $formulaire->addElement('static', 'abstract'         , 'Résumé', $champs['abstract']);
    foreach ($conferenciers as $conferencier) {
    	$formulaire->addElement('static', 'conferencier_id_'.$conferencier['conferencier_id'], 'Conférencier', $conferencier['nom'].' '.$conferencier['prenom'].' ('.$conferencier['societe'].')');
    }
    $formulaire->addElement('static', 'date_soumission', 'Soumission'     , $champs['date_soumission']);
    $formulaire->addElement('static', 'genre'          , 'Type de session', $genres[$champs['genre']]);

    $formulaire->addElement('header', null, 'Commentaires');
    $commentaires = $forum_appel->obtenirCommentairesPourSession($_GET['id']);
    if (is_array($commentaires)) {
	    foreach ($commentaires as $commentaire) {
	        $formulaire->addElement('static',
	        						'id_commentaire_'.$commentaire['id'],
	                                date('d/m/Y h:i', $commentaire['date']),
	                                $commentaire['commentaire'].'<br /><br /><em>'.$commentaire['nom'].' '.$commentaire['prenom'].'</em>');
	    }
    }


    $conf = $GLOBALS['AFUP_CONF'];

    $formulaire->addElement('header', null, 'Noter');

    if (in_array($_SESSION['afup_login'], $conf->obtenir('bureau'))
            && $forum_appel->dejaVote($droits->obtenirIdentifiant(), $id) === false) {
        $formulaire->addElement('select', 'vote', 'Noter cette session', array(''  => '',
                                                                               '5' => 'Oui',
                                                                               '3' => 'Plutôt oui',
                                                                               '2' => 'Plutôt non',
                                                                               '1' => 'Non'));
	    $formulaire->addElement('header', 'boutons'  , '');
		$formulaire->addElement('submit', 'soumettre', 'Soumettre');
    }

    $formulaire->addElement('submit', 'passer'   , 'Passer');

	if (isset($_POST['passer'])) {
	    $url = 'index.php?page=forum_sessions&action=lister';
	    if ($id_next = $forum_appel->obtenirSessionSuivanteSansVote($id_forum, $droits->obtenirIdentifiant())) {
            $url = 'index.php?page=forum_sessions&action=voter&id=' . $id_next . '&id_forum=' . $id_forum;
        }
        afficherMessage('Direction une autre session sans vote', $url);

	} elseif ($formulaire->validate()) {
        $identifiant = $droits->obtenirIdentifiant();
		$valeurs = $formulaire->exportValues();

        if (isset($valeurs['vote'])
                && !empty($valeurs['vote'])
                && $forum_appel->dejaVote($identifiant, $id) === false) {
            $today = date('Y-m-d');
            $salt = $forum_appel->obtenirGrainDeSel($identifiant);
            $res = $forum_appel->noterLaSession($valeurs['id'], $valeurs['vote'], $salt, $today);
            $forum_appel->aVote($identifiant, $id);
            if ($res) {
                Logs::log($_SESSION['afup_login'] . ' a voté sur la session n°' . $formulaire->exportValue('id'));
                $forum_appel->envoyerResumeVote($salt, $identifiant);
	            $url = 'index.php?page=forum_sessions&action=lister';
	            if ($id_next = $forum_appel->obtenirSessionSuivanteSansVote($id_forum, $droits->obtenirIdentifiant())) {
	                $url = 'index.php?page=forum_sessions&action=voter&id=' . $id_next . '&id_forum=' . $id_forum;
	            }
	            afficherMessage('La note sur la session n°' . $formulaire->exportValue('id'). ' a été enregistrée', $url);
            } else {
	            $smarty->assign('erreur', 'Une erreur est survenue lors de l\'enregistrement du vote sur la session');
            }

        }

    }

    $current = $forum->obtenir($_GET['id_forum'], 'titre');
    $smarty->assign('forum_name', $current['titre']);
    $smarty->assign('formulaire', genererFormulaire($formulaire));

} else {

    $pays = new Pays($bdd);

    $talk = null;
	
    $formulaire = instancierFormulaire();
    if ($action != 'ajouter') {
        $champs = $forum_appel->obtenirSession($_GET['id']);

        $talk = $this->get('ting')->get(TalkRepository::class)->get($_GET['id']);

        $formulaire->setDefaults($champs);

    	if (isset($champs) && isset($champs['id_forum'])) {
    	    $_GET['id_forum'] = $champs['id_forum'];
    	}
    }

    $id = isset($_GET['id']) ? $_GET['id'] : 0;
	$formulaire->addElement('hidden', 'id'      , $id);
	$formulaire->addElement('hidden', 'id_forum', $_GET['id_forum']);

    $formulaire->addElement('header', null, 'Présentation');

    $formulaire->addElement('date'    , 'date_soumission', 'Soumission', array('language' => 'fr', 'minYear' => date('Y'), 'maxYear' => date('Y')));
    $formulaire->addElement('text'    , 'titre'          , 'Titre' , array('size' => 40, 'maxlength' => 150));

    $abstractClass = 'simplemde';
    $useMarkdown = true;
    if ($talk !== null && $talk->getUseMarkdown() === false) {
        $useMarkdown = false;
        $abstractClass = 'tinymce';
    }

    $formulaire->addElement('textarea', 'abstract'       , 'Résumé', array('cols' => 40, 'rows' => 15,'class'=> $abstractClass));
    $formulaire->addElement('hidden', 'use_markdown', (int)$useMarkdown);


    $typesLabelsByKey = \AppBundle\Event\Model\Talk::getTypeLabelsByKey();
    asort($typesLabelsByKey);
    $groupe = array();
    foreach ($typesLabelsByKey as $genreKey => $genreLabel) {
        $groupe[] = $formulaire->createElement('radio', 'genre', null, $genreLabel, $genreKey);
    }
    $formulaire->addGroup($groupe, 'groupe_type_pres', "Type de session", '<br />', false);

    $groupe = array();
    $groupe[] = $formulaire->createElement('radio', 'plannifie', null, 'Oui', 1);
    $groupe[] = $formulaire->createElement('radio', 'plannifie', null, 'Non', 0);
    $formulaire->addGroup($groupe, 'groupe_plannifie', "Plannifi&eacute;", '<br />', false);

    $groupe = array();

    $groupe[] = $formulaire->createElement('radio', 'skill', null, 'N/A', Talk::SKILL_NA);
    $groupe[] = $formulaire->createElement('radio', 'skill', null, 'Junior', Talk::SKILL_JUNIOR);
    $groupe[] = $formulaire->createElement('radio', 'skill', null, 'Medior', Talk::SKILL_MEDIOR);
    $groupe[] = $formulaire->createElement('radio', 'skill', null, 'Senior', Talk::SKILL_SENIOR);
    $formulaire->addGroup($groupe, 'groupe_skill', "Niveau", '<br />', false);

    $formulaire->addElement('checkbox'    , 'needs_mentoring'          , "Demande a bénéficier du programme d'accompagnement des jeunes speakers");


    if ($action != 'ajouter') {
        $formulaire->addElement('text'    , 'joindin'          , 'Id de la conférence chez joind.in' , array('size' => 40, 'maxlength' => 10));
        $formulaire->addElement('text'    , 'youtube_id'          , 'Id de la conférence sur youtube' , array('size' => 40, 'maxlength' => 30));
        $formulaire->addElement('text'    , 'slides_url'          , 'URL où trouver les slides' , array('size' => 80, 'maxlength' => 255));
        $formulaire->addElement('text'    , 'blog_post_url'          , 'URL de la version  article de blog de la conférence' , array('size' => 80, 'maxlength' => 255));
        $formulaire->addElement('select', 'language_code', 'Langue', Talk::getLanguageLabelsByKey());

        $formulaire->addElement('checkbox'    , 'video_has_fr_subtitles'          , "Sous titres FR présents");
        $formulaire->addElement('checkbox'    , 'video_has_en_subtitles'          , "Sous titres EN présents");
        $formulaire->addElement('date'  , 'date_publication'       , 'Date de publication'               , array('language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5));
    }


    $formulaire->addElement('header', null, 'Conférencier(s)');
    $conferenciers = array(null => '' ) + $forum_appel->obtenirListeConferenciers($_GET['id_forum'], 'c.conferencier_id, CONCAT(c.nom, " ", c.prenom) as nom', 'c.nom, c.conferencier_id', true);
	$formulaire->addElement('select', 'conferencier_id_1'    , 'N°1', $conferenciers);
	$formulaire->addElement('select', 'conferencier_id_2'    , 'N°2', $conferenciers);
    $formulaire->addElement('select', 'conferencier_id_3'    , 'N°3', $conferenciers);

	if ($action != 'ajouter') {
        $conferenciers = $forum_appel->obtenirConferenciersPourSession($id);
		$formulaire->addElement('header'  , ''                   , 'Conférenciers associés');
		foreach ($conferenciers as $conferencier) {
            $nom = $conferencier['nom'] . ' ' . $conferencier['prenom'][0];
            $formulaire->addElement('static', 'info', $nom . '.',
		    '<a href="index.php?page=forum_conferenciers&action=modifier&id=' . $conferencier['conferencier_id'] . '" title="Voir la fiche du conférencier">Voir la fiche</a>');
        }
    }

    $formulaire->addElement('header', null, 'Commentaires');
    $commentaires = $forum_appel->obtenirCommentairesPourSession($id);
    if (is_array($commentaires)) {
      foreach ($commentaires as $commentaire) {
          $formulaire->addElement('static',
                      'id_commentaire_'.$commentaire['id'],
                                  date('d/m/Y h:i', $commentaire['date']),
                                  $commentaire['commentaire'].'<br /><br /><em>'.$commentaire['nom'].' '.$commentaire['prenom'].'</em>');
      }
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
                $valeurs['date_soumission']['Y'].'-'.$valeurs['date_soumission']['M'].'-'.$valeurs['date_soumission']['d'],
                $valeurs['titre'],
                $valeurs['abstract'],
                $valeurs['genre'],
                $valeurs['plannifie'],
                isset($valeurs['needs_mentoring']) ? $valeurs['needs_mentoring'] : 0,
                $valeurs['skill'],
                $valeurs['use_markdown']
            );
			$ok = (bool)$session_id;
        } else {
            $session_id = (int)$_GET['id'];
            $ok = $forum_appel->modifierSession($session_id,
            								    $valeurs['id_forum'],
                                                $valeurs['date_soumission']['Y'].'-'.$valeurs['date_soumission']['M'].'-'.$valeurs['date_soumission']['d'],
			                                    $valeurs['titre'],
			                                    $valeurs['abstract'],
			                                    $valeurs['genre'],
			                                    $valeurs['plannifie'],
                                                $valeurs['joindin'],
                                                $valeurs['youtube_id'],
                                                $valeurs['slides_url'],
                                                $valeurs['blog_post_url'],
                                                $valeurs['language_code'],
                                                $valeurs['skill'],
                                                $valeurs['needs_mentoring'],
                                                $valeurs['use_markdown'],
                                                $valeurs['video_has_fr_subtitles'],
                                                $valeurs['video_has_en_subtitles'],
                                                $valeurs['date_publication']['Y'].'-'.$valeurs['date_publication']['M'].'-'.$valeurs['date_publication']['d'] . ' ' . $valeurs['date_publication']['H'] . ':' . $valeurs['date_publication']['i'] . ':' . $valeurs['date_publication']['s']
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
            afficherMessage('La session a été ' . (($action == 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=forum_sessions&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de la session');
        }
    }

    $current = $forum->obtenir($_GET['id_forum'], 'titre');
    $smarty->assign('forum_name', $current['titre']);
    $smarty->assign('formulaire', genererFormulaire($formulaire));
    $smarty->assign('talk', $talk);
}
