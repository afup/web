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


    if (!isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0) {
        $_GET['id_forum'] = $forum->obtenirDernier();
    }

    $rs_forum = $forum->obtenir( $_GET['id_forum']);
    $annee_forum = $rs_forum['forum_annee'];

    $sessions_rs = $forum_appel->obtenirListeSessionsPlannifies($_GET['id_forum']);
    $salles = $forum_appel->obtenirListeSalles($_GET['id_forum'], true);


    foreach ($sessions_rs as $session)
    {
      $session_id = $session['session_id'];
      //var_dump($session);
    	$formulaire = &instancierFormulaire();;
    	$champs = $forum_appel->obtenirPlanningDeSession($session_id);
    	$champs['date']= $champs['debut'];
      $formulaire->setDefaults($champs);
      $id = isset($_POST['id']) ? $_POST['id'] : 0;



      $formulaire->addElement('header', null, 'Plannification');
      $formulaire->addElement('hidden', 'id'      , null);
      $formulaire->addElement('hidden', 'id_session', $session_id);
      $formulaire->addElement('hidden', 'id_forum', $champs['id_forum']);
      $formulaire->addElement('date'    , 'date'   , 'Date', array('language' => 'fr', 'format' => "dMY", 'minYear' => $annee_forum, 'maxYear' => $annee_forum));
      $formulaire->addElement('date'    , 'debut'   , 'Heure debut', array('language' => 'fr', 'format' => "H:i", 'minHour' => 8, 'maxHour' => 18, 'optionIncrement' => array('i' => 15)));
      $formulaire->addElement('date'    , 'fin'     , 'Heure Fin'  , array('language' => 'fr', 'format' => "H:i", 'optionIncrement' => array('i' => 15), 'minHour' => 8, 'maxHour' => 18));
      $formulaire->addElement('select'  , 'id_salle', 'Salle', array(null => '' ) + $salles);

      $groupe = array();
      $groupe[] = &HTML_QuickForm::createElement('radio', 'keynote', null, 'Oui', 1);
      $groupe[] = &HTML_QuickForm::createElement('radio', 'keynote', null, 'Non', 0);
      $formulaire->addGroup($groupe, 'groupe_keynote', "Keynote", '<br />', false);

      $formulaire->addElement('header', 'boutons'  , '');
      $formulaire->addElement('submit', 'soumettre', 'Soumettre');
               // var_dump($_POST,$formulaire, genererFormulaire($formulaire), $valeurs,$ok);die;
          if (isset($_POST['id_session']) && $_POST['id_session']  == $session_id && $formulaire->validate()) {

    $valeurs = $formulaire->exportValues();
            $ok = $forum_appel->modifierSessionDuPlanning($valeurs['id'],
                $valeurs['id_forum'],
                $valeurs['id_session'],
                mktime($valeurs['debut']['H'], $valeurs['debut']['i'], 0, $valeurs['date']['M'], $valeurs['date']['d'], $valeurs['date']['Y']),
                mktime($valeurs['fin']['H'], $valeurs['fin']['i'], 0, $valeurs['date']['M'], $valeurs['date']['d'], $valeurs['date']['Y']),
                $valeurs['id_salle'],
                $valeurs['keynote']
                );
      afficherMessage('Le planning de la session a été modifé' , 'index.php?page=forum_planning&action=lister');

          }
      $session['formulaire'] = genererFormulaire($formulaire);
      $sessions[]=$session;

    }

    $smarty->assign('agenda', $forum->genAgenda($annee_forum,true));
    $smarty->assign('id_forum', $_GET['id_forum']);

    $smarty->assign('forums', $forum->obtenirListe());

    $smarty->assign('sessions',$sessions);

} elseif ($action == 'supprimer') {
    if ($forum_appel->supprimerSessionDuPlanning($_GET['id'])) {
        AFUP_Logs::log('Suppression de la programmation de la session ' . $_GET['id']);
        afficherMessage('La programmation de la session a été supprimée', 'index.php?page=forum_planning&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de la session', 'index.php?page=forum_planning&action=lister', true);
    }

} else {
    require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Pays.php';
    $pays = new AFUP_Pays($bdd);
    $formulaire = &instancierFormulaire();

    $champs = $forum_appel->obtenirPlanningDeSession($_GET['id_session']);
    $conferenciers = $forum_appel->obtenirConferenciersPourSession($_GET['id_session']);

    if (empty($champs['debut']) && empty($champs['fin'])) {
        $id_forum = $forum->obtenirDernier();
        $forum_donnees = $forum->obtenir($id_forum);
        $champs['debut'] = $forum_donnees['date_debut'];
        $champs['fin'] = $forum_donnees['date_debut'];
    }

    $formulaire->setDefaults($champs);
    $id = isset($_GET['id']) ? $_GET['id'] : 0;

	$formulaire->addElement('hidden', 'id'      , null);
	$formulaire->addElement('hidden', 'id_session', $_GET['id_session']);
	$formulaire->addElement('hidden', 'id_forum', $champs['id_forum']);

    $formulaire->addElement('header', null, 'Présentation');
    $formulaire->addElement('static', 'titre'   , 'Titre' , '<strong>'.$champs['titre'].'</strong>');
    $formulaire->addElement('static', 'abstract', 'Résumé', $champs['abstract']);

    foreach ($conferenciers as $conferencier) {
      $formulaire->addElement('static', 'conferencier_id_'.$conferencier['conferencier_id'], 'Conférencier', $conferencier['nom'].' '.$conferencier['prenom'].' ('.$conferencier['societe'].')');
    }

    $formulaire->addElement('header', null, 'Plannification');
    $formulaire->addElement('date'    , 'debut'   , 'Début', array('language' => 'fr', 'format' => "dMY H:i", 'minYear' => date('Y'), 'maxYear' => date('Y'), 'minHour' => 8, 'maxHour' => 18, 'optionIncrement' => array('i' => 15)));
    $formulaire->addElement('date'    , 'fin'     , 'Fin'  , array('language' => 'fr', 'format' => "dMY H:i", 'minYear' => date('Y'), 'maxYear' => date('Y'), 'optionIncrement' => array('i' => 15), 'minHour' => 8, 'maxHour' => 18));
    $formulaire->addElement('select'  , 'id_salle', 'Salle', array(null => '' ) + $forum_appel->obtenirListeSalles($champs['id_forum'], true));

	$formulaire->addElement('header', 'boutons'  , '');
	$formulaire->addElement('submit', 'soumettre', 'Soumettre');

	$formulaire->addRule('debut'   , 'Date et heure du début manquants', 'required');
	$formulaire->addRule('fin'     , 'Date et heure de fin manquants'  , 'required');
	$formulaire->addRule('id_salle', 'Nom de la salle manquant'        , 'required');

    if ($formulaire->validate()) {
		$valeurs = $formulaire->exportValues();

		if ($id == 0) {
			$planning_id = $forum_appel->ajouterSessionDansPlanning($valeurs['id_forum'],
                $valeurs['id_session'],
                mktime($valeurs['debut']['H'], $valeurs['debut']['i'], 0, $valeurs['debut']['M'], $valeurs['debut']['d'], $valeurs['debut']['Y']),
                mktime($valeurs['fin']['H'], $valeurs['fin']['i'], 0, $valeurs['fin']['M'], $valeurs['fin']['d'], $valeurs['fin']['Y']),
                $valeurs['id_salle']);


			$ok = (bool)$planning_id;
        } else {
            $planning_id = (int)$_GET['id'];
            $ok = $forum_appel->modifierSessionDuPlanning($planning_id,
                $valeurs['id_forum'],
                $valeurs['id_session'],
                mktime($valeurs['debut']['H'], $valeurs['debut']['i'], 0, $valeurs['debut']['M'], $valeurs['debut']['d'], $valeurs['debut']['Y']),
                mktime($valeurs['fin']['H'], $valeurs['fin']['i'], 0, $valeurs['fin']['M'], $valeurs['fin']['d'], $valeurs['fin']['Y']),
                $valeurs['id_salle']);
        }

        if ($ok) {
            if ($action == 'ajouter') {
                AFUP_Logs::log('Ajout du planning de la session de ' . $formulaire->exportValue('titre'));
            } else {
                AFUP_Logs::log('Modification du planning de la session de ' . $formulaire->exportValue('titre') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('Le planning de la session a été ' . (($action == 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=forum_planning&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' du planning de la session');
        }
    }

    $current = $forum->obtenir($champs['id_forum'], 'titre');
    $smarty->assign('forum_name', $current['titre']);
    $smarty->assign('formulaire', genererFormulaire($formulaire));
}

?>
