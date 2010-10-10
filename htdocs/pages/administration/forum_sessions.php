<?php

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'commenter', 'supprimer', 'voter'));
$tris_valides = array();
$sens_valides = array('asc' , 'desc');
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_AppelConferencier.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Droits.php';

$forum = new AFUP_Forum($bdd);
$forum_appel = new AFUP_AppelConferencier($bdd);
$droits = new AFUP_Droits($bdd);

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
    if (isset($_GET['type'])) {
        $list_type = $_GET['type'];
    }

    if (!isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0) {
        $_GET['id_forum'] = $forum->obtenirDernier();
    }
    $smarty->assign('id_forum', $_GET['id_forum']);
    $smarty->assign('list_type', $list_type);

    $smarty->assign('forums', $forum->obtenirListe());
    $smarty->assign('sessions', $forum_appel->obtenirListeSessions($_GET['id_forum'], $list_champs, $list_ordre, $list_associatif, $list_filtre,$list_type));
} elseif ($action == 'supprimer') {
    if ($forum_appel->supprimerSession($_GET['id'])) {
        AFUP_Logs::log('Suppression de la session ' . $_GET['id']);
        afficherMessage('La session a été supprimée', 'index.php?page=forum_sessions&action=lister&type='.$list_type);
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de la session', 'index.php?page=forum_sessions&action=lister&type='.$list_type, true);
    }

} elseif ($action == 'commenter') {
    $journees = array();
    $journees[1] = 'Fonctionnel';
    $journees[2] = 'Technique';
    $journees[3] = 'Les deux';

    $genres = array();
    $genres[1] = 'Conférence plénière';
    $genres[2] = 'Atelier';
    $genres[9] = 'Projet PHP';

    $formulaire = &instancierFormulaire();
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
    $formulaire->addElement('static', 'journee'        , 'Public visé'    , $journees[$champs['journee']]);
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

    require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Configuration.php';
    $conf = $GLOBALS['AFUP_CONF'];

    if (in_array($_SESSION['afup_login'], $conf->obtenir('bureau'))
            && $forum_appel->dejaVote($droits->obtenirIdentifiant(), $id) === false) {
        $formulaire->addElement('header', null, 'Noter');
        $formulaire->addElement('select', 'vote', 'Noter cette session', array(''  => '',
                                                                               '5' => 'Oui',
                                                                               '3' => 'Plutôt oui',
                                                                               '2' => 'Plutôt non',
                                                                               '1' => 'Non'));
    }

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

        if (isset($valeurs['vote'])
                && !empty($valeurs['vote'])
                && $forum_appel->dejaVote($identifiant, $id) === false) {
            $today = date('Y-m-d');
            $salt = $forum_appel->obtenirGrainDeSel($identifiant);
            $res = $forum_appel->noterLaSession($valeurs['id'], $valeurs['vote'], $salt, $today);
            $forum_appel->aVote($identifiant, $id);
            if ($res) {
                AFUP_Logs::log($_SESSION['afup_login'] . ' a voté sur la session n°' . $formulaire->exportValue('id'));
                $forum_appel->envoyerResumeVote($salt, $identifiant);
	            $url = 'index.php?page=forum_sessions&action=lister';
	            if ($id_next = $forum_appel->obtenirSessionSuivanteSansVote($id_forum, $droits->obtenirIdentifiant())) {
	                $url = 'index.php?page=forum_sessions&action=commenter&id=' . $id_next . '&id_forum=' . $id_forum;
	            }
	            afficherMessage('La note sur la session n°' . $formulaire->exportValue('id'). ' a été enregistrée', $url);
            } else {
	            $smarty->assign('erreur', 'Une erreur est survenue lors de l\'enregistrement du vote sur la session');
            }

        } elseif (!empty($valeurs['commentaire'])) {
	        $ok = $forum_appel->ajouterCommentaire($id,
				                                   $identifiant,
	            								   $valeurs['commentaire'],
				                                   time(),
				                                   0);

	        if ($ok) {
	            AFUP_Logs::log('Ajout d\'un commentaire sur la session n°' . $formulaire->exportValue('id'));
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

} else {
    require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Pays.php';
    $pays = new AFUP_Pays($bdd);

    $formulaire = &instancierFormulaire();
    if ($action != 'ajouter') {
        $champs = $forum_appel->obtenirSession($_GET['id']);

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
    $formulaire->addElement('text'    , 'titre'          , 'Titre' , array('size' => 40, 'maxlength' => 80));
    $formulaire->addElement('textarea', 'abstract'       , 'Résumé', array('cols' => 40, 'rows' => 15,'class'=>'tinymce'));

    $groupe = array();
    $groupe[] = &HTML_QuickForm::createElement('radio', 'journee', null, 'Fonctionnel', 1);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'journee', null, 'Technique'  , 2);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'journee', null, 'Les deux'   , 3);
    $formulaire->addGroup($groupe, 'groupe_pres', "Public visé", '<br />', false);

    $groupe = array();
    $groupe[] = &HTML_QuickForm::createElement('radio', 'genre', null, 'Conférence plénière', 1);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'genre', null, 'Atelier'            , 2);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'genre', null, 'Projet'            , 9);
    $formulaire->addGroup($groupe, 'groupe_type_pres', "Type de session", '<br />', false);

    $groupe = array();
    $groupe[] = &HTML_QuickForm::createElement('radio', 'plannifie', null, 'Oui', 1);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'plannifie', null, 'Non', 0);
    $formulaire->addGroup($groupe, 'groupe_plannifie', "Plannifi&eacute;", '<br />', false);

    $formulaire->addElement('header', null, 'Conférencier(s)');
    $conferenciers = array(null => '' ) + $forum_appel->obtenirListeConferenciers($_GET['id_forum'], 'c.conferencier_id, CONCAT(c.nom, " ", c.prenom) as nom', 'nom', true);
	$formulaire->addElement('select', 'conferencier_id_1'    , 'N°1', $conferenciers);
	$formulaire->addElement('select', 'conferencier_id_2'    , 'N°2', $conferenciers);
    
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

		if ($action == 'ajouter') {
			$session_id = $forum_appel->ajouterSession($valeurs['id_forum'],
                                                       $valeurs['date_soumission']['Y'].'-'.$valeurs['date_soumission']['M'].'-'.$valeurs['date_soumission']['d'],
			                                           $valeurs['titre'],
			                                           $valeurs['abstract'],
			                                           $valeurs['journee'],
			                                           $valeurs['genre'],
			                                           $valeurs['plannifie']);
			$ok = (bool)$session_id;
        } else {
            $session_id = (int)$_GET['id'];
            $ok = $forum_appel->modifierSession($session_id,
            								    $valeurs['id_forum'],
                                                $valeurs['date_soumission']['Y'].'-'.$valeurs['date_soumission']['M'].'-'.$valeurs['date_soumission']['d'],
			                                    $valeurs['titre'],
			                                    $valeurs['abstract'],
			                                    $valeurs['journee'],
			                                    $valeurs['genre'],
			                                    $valeurs['plannifie']);
            $forum_appel->delierSession($session_id);
        }

        if ($ok) {
            $ok &= $forum_appel->lierConferencierSession($valeurs['conferencier_id_1'], $session_id);
            $ok &= $forum_appel->lierConferencierSession($valeurs['conferencier_id_2'], $session_id);
        }

        if ($ok) {
            if ($action == 'ajouter') {
                AFUP_Logs::log('Ajout de la session de ' . $formulaire->exportValue('titre'));
            } else {
                AFUP_Logs::log('Modification de la session de ' . $formulaire->exportValue('titre') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('La session a été ' . (($action == 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=forum_sessions&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de la session');
        }
    }

    $current = $forum->obtenir($_GET['id_forum'], 'titre');
    $smarty->assign('forum_name', $current['titre']);
    $smarty->assign('formulaire', genererFormulaire($formulaire));
}

?>