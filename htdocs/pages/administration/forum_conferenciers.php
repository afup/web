<?php

// Impossible to access the file itself
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer','inscrire_forum', 'associer_gravatar'));
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

  $valeurs['id_forum']= (int)$_GET['id_forum'];

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
        afficherMessage($nb_conferencier . ' conférenciers ont été ajoutés dans les inscriptions', 'index.php?page=forum_conferenciers&action=lister');
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
    $listeConferenciers = $forum_appel->obtenirListeConferenciers($_GET['id_forum'], $list_champs, $list_ordre, $list_associatif, $list_filtre);
    foreach ($listeConferenciers as &$conferencier) {
        $conferencier['sessions'] = $forum_appel->obtenirListeSessionsPourConferencier($_GET['id_forum'], $conferencier['conferencier_id']);
    }
    $smarty->assign('conferenciers', $listeConferenciers);
    $smarty->assign('nb_conferenciers', $forum_appel->obtenirNbConferenciersDistinct($_GET['id_forum']));
} elseif ($action == 'supprimer') {
    if ($forum_appel->supprimerConferencier($_GET['id'])) {
        AFUP_Logs::log('Suppression du conférencier ' . $_GET['id']);
        afficherMessage('Le conférencier a été supprimé', 'index.php?page=forum_conferenciers&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression du conférencier', 'index.php?page=forum_conferenciers&action=lister', true);
    }
} elseif ($action == 'associer_gravatar') {
    $champs = $forum_appel->obtenirConferencier($_GET['id']);
    $rs = $forum->obtenir( $_GET['id_forum']);
    $imageDir = realpath('../../templates/'.$rs['path'].'/images/intervenants/');
    // Transformation en 90x120 JPG pour simplifier
    $img = @imagecreatefromjpeg(AFUP_Utils::get_gravatar($champs['email'], 90));
    if (gettype($img) != 'resource') {
      $img = imagecreatefrompng(AFUP_Utils::get_gravatar($champs['email'], 90));
    }
    $width = imagesx($img);
    $height = imagesy($img);
    /*if ($width != 90 || $height != 120) {
        $oldImg = $img;
        $img = imagecreatetruecolor(90, 120);
        imagecopyresampled($img, $oldImg, 0, 0, 0, 0, 90, 120, $width, $height);
    }*/
    imagejpeg($img, $imageDir . '/' . $_GET['id'] . '.jpg', 90);
    chmod($imageDir . '/' . $_GET['id'] . '.jpg', 0664);
    afficherMessage('L\'image gravatar a été associée', 'index.php?page=forum_conferenciers&action=modifier&id='. $_GET['id'] . '&id_forum=' . $_GET['id_forum']);
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
    $formulaire->addElement('text'  , 'twitter'     , 'Nickname Twitter', array('size' => 50, 'maxlength' => 100));
    if($_GET['id']) {
        $formulaire->addElement('file', 'photo', 'Photo (90x120)'     );
    }
    if ($action == 'modifier') {
        $formulaire->addElement('static'  , 'html'                     , '', '<img src="/templates/'.$rs['path'].'/images/intervenants/' . $_GET['id'] . '.jpg" /><br />');
        $chemin = realpath('../../templates/'.$rs['path'].'/images/intervenants/' . $_GET['id'] . '.jpg');
        if (file_exists($chemin)) {
            if ((function_exists('getimagesize'))) {
                $info = getimagesize($chemin);
                $formulaire->addElement('static'  , 'html'                     , '', 'Taille actuelle : ' . $info[3]);
                $formulaire->addElement('static'  , 'html'                     , '', 'Type MIME : ' . $info['mime']);
            } else {
                $formulaire->addElement('static'  , 'html'                     , '', 'L\'extension GD n\'est pas présente sur ce serveur');
            }
        }
    }

  //$sessions = $forum_appel->obtenirListeSessions($_GET['id_forum'], $list_champs, $list_ordre, $list_associatif, $list_filtre,$list_type));
  $sessions = $forum_appel->obtenirListeSessionsPourConferencier($_GET['id_forum'],$_GET['id']);

  $formulaire->addElement('header', null          , 'Sessions');
  foreach ($sessions as $session) {
    $url = 'index.php?page=forum_sessions&action=commenter&id=' . $session['session_id'] . '&id_forum=' . $_GET['id_forum'];
    $formulaire->addElement('static', null  , '<a href="'.$url.'">'.$session['titre'].'</a>');
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
            $ok = $forum_appel->modifierConferencier($_GET['id'],
                                                     $valeurs['id_forum'],
                              											 $valeurs['civilite'],
                              											 $valeurs['nom'],
                                                     $valeurs['prenom'],
                                                     $valeurs['email'],
                                                     $valeurs['societe'],
                                                     $valeurs['biographie'],
                                                     $valeurs['twitter']);
            $file = $formulaire->getElement('photo');
            $data = $file->getValue();
            if ($data['name']) {
                $imageDir = realpath('../../templates/'.$rs['path'].'/images/intervenants/');
                // Transformation en 90x120 JPG pour simplifier
                $data = $file->getValue();
                if ($data['type'] == 'image/png') {
                    $img = imagecreatefrompng($data['tmp_name']);
                } else {
                    $img = imagecreatefromjpeg($data['tmp_name']);
                }
                $width = imagesx($img);
                $height = imagesy($img);
                if ($width != 90 || $height != 120) {
                    $oldImg = $img;
                    $img = imagecreatetruecolor(90, 120);
                    imagecopyresampled($img, $oldImg, 0, 0, 0, 0, 90, 120, $width, $height);
                }
                imagejpeg($img, $imageDir . '/' . $_GET['id'] . '.jpg', 90);
            }
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
    if ($action == 'modifier') {
      $smarty->assign('id_conferencier', $_GET['id']);
      $smarty->assign('id_forum', $_GET['id_forum']);
      $smarty->assign('gravatar', AFUP_Utils::get_gravatar($champs['email'], 90));
    }
    $smarty->assign('forum_name', $current['titre']);
    $smarty->assign('formulaire', genererFormulaire($formulaire));
}

?>