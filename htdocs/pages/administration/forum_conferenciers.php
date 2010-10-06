<?php

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer','inscrire_forum'));
$tris_valides = array();
$sens_valides = array('asc' , 'desc');
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_AppelConferencier.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Inscriptions_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Facturation_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';

$forum = new AFUP_Forum($bdd);
$forum_appel = new AFUP_AppelConferencier($bdd);
$forum_inscriptions = new AFUP_Inscriptions_Forum($bdd);
$forum_facturation = new AFUP_Facturation_Forum($bdd);
if ($action == 'inscrire_forum')
{

    if (!isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0) {
        $_GET['id_forum'] = $forum->obtenirDernier();
    }
  $sessions = $forum_appel->obtenirListeSessionsPlannifies($_GET['id_forum'] );
  foreach (array(353,354,355,356,357,358,359,361,362,363,364,366) as $id_projet_php)
  {
  	 $sessions[] = array('session_id'=> $id_projet_php,'is_projet'=> true);
  }

  $valeurs['id_forum']= 4;

  $nb_conferencier = 0;
  $valeurs['citer_societe'] = true;
  $valeurs['newsletter_nexen'] = true;
  $valeurs['newsletter_afup'] = true;
  $valeurs['type_reglement'] = 3;
  $valeurs['etat'] = 5;
  $valeurs['id_pays_facturation'] = 'FR';

  $key_vides = array('old_reference','coupon','telephone'
                    ,'adresse_facturation','code_postal_facturation'
                    ,'ville_facturation','autorisation'
                    ,' transaction','autorisation'
                    ,'informations_reglement','facturation'
                    ,'date_reglement');
  foreach ($key_vides as $key_vide)
  {
    $valeurs[trim($key_vide)] = '';
  }

  foreach ($sessions as $index => $session)
  {
    $conferenciers = $forum_appel->obtenirConferenciersPourSession($session['session_id']);
     $valeurs['type_inscription'] = array_key_exists('is_projet',$session) ?AFUP_FORUM_PROJET:AFUP_FORUM_CONFERENCIER;
    foreach ($conferenciers as $conferencier)
    {


      $valeurs['reference'] = 'GENCONF-'.$valeurs['id_forum'].'-'.$conferencier['conferencier_id'].'-'.$valeurs['type_inscription'];
      $valeurs['commentaires'] = 'import auto';
      $valeurs['societe_facturation'] = 3;
      $valeurs['civilite'] = $conferencier['civilite'];
      $valeurs['nom'] = $conferencier['nom'];
      $valeurs['prenom'] = $conferencier['prenom'];
      $valeurs['nom_facturation'] = $valeurs['nom'];
      $valeurs['prenom_facturation'] = $valeurs['prenom'];
      $valeurs['email'] = $conferencier['email'];
      $valeurs['email_facturation'] =$valeurs['email'];
      $valeurs['societe_facturation'] = $conferencier['societe'];


      if (!$forum_facturation->obtenir($valeurs['reference']))
      {

       $ok_inscrit =  $forum_inscriptions->ajouterInscription($valeurs['id_forum'],
        $valeurs['reference'],
        $valeurs['type_inscription'],
        $valeurs['civilite'],
        $valeurs['nom'],
        $valeurs['prenom'],
        $valeurs['email'],
        $valeurs['telephone'],
        $valeurs['coupon'],
        $valeurs['citer_societe'],
        $valeurs['newsletter_afup'],
        $valeurs['newsletter_nexen'],
        $valeurs['commentaires'],
        $valeurs['etat'],
        $valeurs['facturation']);;

        if ($ok_inscrit)
        {

        $ok_fact =  $forum_facturation->gererFacturation($valeurs['reference'],
        $valeurs['type_reglement'],
        $valeurs['informations_reglement'],
        $valeurs['date_reglement'],
        $valeurs['email_facturation'],
        $valeurs['societe_facturation'],
        $valeurs['nom_facturation'],
        $valeurs['prenom_facturation'],
        $valeurs['adresse_facturation'],
        $valeurs['code_postal_facturation'],
        $valeurs['ville_facturation'],
        $valeurs['id_pays_facturation'],
        $valeurs['id_forum'],
        $valeurs['old_reference'],
        $valeurs['autorisation'],
        $valeurs['transaction'],
        $valeurs['etat']);


        if ($ok_fact)
        {
             AFUP_Logs::log('Ajout inscription conférencier ' . $conferencier['conferencier_id']);
             $nb_conferencier++;

        }
        else
        {
             afficherMessage('Une erreur est survenue lors de \'ajout de la facturation', 'index.php?page=forum_conferenciers&action=lister', true);
        }


        }
        else
        {
            afficherMessage('Une erreur est survenue lors de \'ajout de l\inscription', 'index.php?page=forum_conferenciers&action=lister', true);
        }

      }

    }

  }
        afficherMessage($nb_conferencier . ' conférenciers a été ajoutés dans les inscriptions', 'index.php?page=forum_conferenciers&action=lister');
}

elseif ($action == 'lister') {
    // Valeurs par défaut des paramètres de tri
    $list_champs = 'c.*';
    $list_ordre = 'c.nom';
    $list_sens = 'desc';
    $list_associatif = false;
    $list_filtre = false;

    // Modification des paramètres de tri en fonction des demandes passées en GET
    if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides)
        && isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
        $list_ordre = $_GET['tri'] . ' ' . $_GET['sens'];
    }
    if (isset($_GET['filtre'])) {
        $list_filtre = $_GET['filtre'];
    }

    if (!isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0) {
        $_GET['id_forum'] = $forum->obtenirDernier();
    }


    $smarty->assign('id_forum', $_GET['id_forum']);

    $smarty->assign('forums', $forum->obtenirListe());
    $smarty->assign('conferenciers', $forum_appel->obtenirListeConferenciers($_GET['id_forum'], $list_champs, $list_ordre, $list_associatif, $list_filtre));
} elseif ($action == 'supprimer') {
    if ($forum_appel->supprimerConferencier($_GET['id'])) {
        AFUP_Logs::log('Suppression du conférencier ' . $_GET['id']);
        afficherMessage('Le conférencier a été supprimé', 'index.php?page=forum_conferenciers&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression du conférencier', 'index.php?page=forum_conferenciers&action=lister', true);
    }
} else {
    require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Pays.php';
    $pays = new AFUP_Pays($bdd);

    $formulaire = &instancierFormulaire();
    if ($action == 'ajouter') {
		$formulaire->setDefaults(array('civilite'            => 'M.',
									   'id_pays_facturation' => 'FR',
									   'type_inscription'    => -1,
									   'type_reglement'      => -1));
    } else {
        $champs = $forum_appel->obtenirConferencier($_GET['id']);
        $formulaire->setDefaults($champs);

    	if (isset($champs) && isset($champs['id_forum'])) {
    	    $_GET['id_forum'] = $champs['id_forum'];
    	}
    }
      $rs = $forum->obtenir( $_GET['id_forum']);
      $annee_forum = $rs['forum_annee'];
    //var_dump($rs,$annee_forum);
	$formulaire->addElement('hidden', 'id_forum', $_GET['id_forum']);
	$_GET['id'] = (!isset($_GET['id'])) ? 0 : (int)$_GET['id'];
	$formulaire->addElement('hidden', 'id', $_GET['id']);

	$formulaire->addElement('header', null          , 'Conférencier');
	$formulaire->addElement('select', 'civilite'    , 'Civilité'       , array('M.' => 'M.', 'Mme' => 'Mme', 'Mlle' => 'Mlle'));
    $formulaire->addElement('text'  , 'nom'         , 'Nom'            , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'  , 'prenom'      , 'Prénom'         , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'  , 'email'       , 'Email'          , array('size' => 30, 'maxlength' => 100));
    $formulaire->addElement('text'  , 'societe'     , 'Société'        , array('size' => 50, 'maxlength' => 100));
    $formulaire->addElement('textarea', 'biographie', 'Biographie'     , array('cols' => 40, 'rows' => 15));
    if($_GET['id'])
    {
    $formulaire->addElement('file', 'photo', 'Photo (90x120)'     );

    }

	$formulaire->addElement('header', 'boutons'  , '');
	$formulaire->addElement('submit', 'soumettre', 'Soumettre');

	// On ajoute les règles
	$formulaire->addRule('nom'      , 'Nom manquant'             , 'required');
	$formulaire->addRule('email'    , 'Email manquant'           , 'required');
	$formulaire->addRule('email'    , 'Email invalide'           , 'email');

    if ($formulaire->validate()) {
		$valeurs = $formulaire->exportValues();

        if ($action == 'ajouter') {
            $ok = $forum_appel->ajouterConferencier($valeurs['id_forum'],
        											$valeurs['civilite'],
        											$valeurs['nom'],
        											$valeurs['prenom'],
        											$valeurs['email'],
        											$valeurs['societe'],
        											$valeurs['biographie']);
        } else {
          $file =& $formulaire->getElement('photo');
        $file->moveUploadedFile(AFUP_CHEMIN_RACINE . 'templates/forumphp'.$annee_forum.'/images/intervenants',$_GET['id'].'.jpg');
            $ok = $forum_appel->modifierConferencier($_GET['id'],
                                                     $valeurs['id_forum'],
        											 $valeurs['civilite'],
        											 $valeurs['nom'],
                                                     $valeurs['prenom'],
                                                     $valeurs['email'],
                                                     $valeurs['societe'],
                                                     $valeurs['biographie']);
        }

        if ($ok) {
            if ($action == 'ajouter') {
                AFUP_Logs::log('Ajout du conférencier de ' . $formulaire->exportValue('prenom') . ' ' . $formulaire->exportValue('nom'));
            } else {
                AFUP_Logs::log('Modification du conférencier de ' . $formulaire->exportValue('prenom') . ' ' . $formulaire->exportValue('nom') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('Le conférencier a été ' . (($action == 'ajouter') ? 'ajouté' : 'modifié'), 'index.php?page=forum_conferenciers&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' du conférencier');
        }
    }

    $current = $forum->obtenir($_GET['id_forum'], 'titre');
    $smarty->assign('forum_name', $current['titre']);
    $smarty->assign('formulaire', genererFormulaire($formulaire));
}

?>