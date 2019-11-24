<?php

// Impossible to access the file itself
use Afup\Site\Forum\Coupon;
use Afup\Site\Forum\Forum;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer', 'ajouter_coupon', 'supprimer_coupon'));
$smarty->assign('action', $action);



$forums = new Forum($bdd);
$coupons = new Coupon($bdd);

if ($action == 'lister') {
    $evenements = $forums->obtenirListe(null, '*', 'date_debut desc');
    foreach ($evenements as &$e) {
        $e['supprimable'] = $forums->supprimable($e['id']);
        $e['coupons'] = $coupons->obtenirCouponsForum($e['id']);
    }
    $smarty->assign('evenements', $evenements);
} elseif ($action == 'ajouter_coupon') {
    if ($coupons->ajouter($_GET['id_forum'], $_GET['coupon'])) {
        Logs::log('Ajout du coupon de forum');
        afficherMessage('Le coupon a été ajouté', 'index.php?page=forum_gestion&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de l\'ajout du coupon', 'index.php?page=forum_gestion&action=lister', true);
    }
} elseif ($action == 'supprimer_coupon') {
    if ($coupons->supprimer($_GET['id'])) {
        Logs::log('Suppression du coupon de forum ' . $_GET['id']);
        afficherMessage('Le coupon a été supprimé', 'index.php?page=forum_gestion&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression du coupon', 'index.php?page=forum_gestion&action=lister', true);
    }
} elseif ($action == 'supprimer') {
    if ($forums->supprimer($_GET['id'])) {
        Logs::log('Suppression du forum ' . $_GET['id']);
        afficherMessage('Le forum a été supprimé', 'index.php?page=forum_gestion&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression du forum', 'index.php?page=forum_gestion&action=lister', true);
    }
} else {
    $formulaire = instancierFormulaire();
    if ($action == 'ajouter') {
		$formulaire->setDefaults(array('civilite' => 'M.',
									   'id_pays'  => 'FR'));
    } else {
        $champs = $forums->obtenir($_GET['id']);
        $champs['coupons'] = implode(', ',$coupons->obtenirCouponsForum($_GET['id']));
        if ($champs['text'] !== null) {
            $text = json_decode($champs['text'], true);
            $champs['cfp_fr'] = $text['fr'];
            $champs['cfp_en'] = $text['en'];
            $champs['speaker_management_fr'] = $text['speaker_management_fr'];
            $champs['speaker_management_en'] = $text['speaker_management_en'];
            $champs['sponsor_management_fr'] = $text['sponsor_management_fr'];
            $champs['sponsor_management_en'] = $text['sponsor_management_en'];
            $champs['mail_inscription_content'] = $text['mail_inscription_content'];
            $champs['become_sponsor_description'] = $text['become_sponsor_description'];
        }

        $formulaire->setDefaults($champs);

    	if (isset($champs) && isset($champs['id'])) {
    	    $_GET['id'] = $champs['id'];
    	}

        $formulaire->addElement('hidden', 'id', $_GET['id']);
    }

    $formulaire->addElement('header', ''                     , 'Gestion de forum');
    $formulaire->addElement('text'  , 'titre'                , 'Titre du forum'                     , array('size' => 30, 'maxlength' => 100));
    $formulaire->addElement('text'  , 'path'                 , 'Chemin du template'                 , array('size' => 30, 'maxlength' => 100));
    $formulaire->addElement('static', 'path_help'            , '', '<i>Le path sert également à déterminer le nom du template de mail à utiliser sur mandrill, sous la forme confirmation-inscription-{PATH}</i>');
    $formulaire->addElement('text'  , 'trello_list_id'       , 'Liste trello pour les leads'        , array('size' => 30, 'maxlength' => 100));
    $formulaire->addElement('text'  , 'logo_url'             , "URL du logo de l'événement"         , array('size' => 30, 'maxlength' => 255));
    $formulaire->addElement('text'  , 'nb_places'            , 'Nombre de places'                   , array('size' => 30, 'maxlength' => 100));
    $formulaire->addElement('text'  , 'place_name'           , 'Nom du lieu'                        , array('size' => 30, 'maxlength' => 255));
    $formulaire->addElement('text'  , 'place_address'        , 'Adresse du lieu'                    , array('size' => 30, 'maxlength' => 255));
    $formulaire->addElement('date'  , 'date_debut'           , 'Date de début'                      , array('language' => 'fr', 'format' => "dMY", 'minYear' => 2001, 'maxYear' => date('Y') + 5));
	$formulaire->addElement('date'  , 'date_fin'             , 'Date de fin'                        , array('language' => 'fr', 'format' => "dMY", 'minYear' => 2001, 'maxYear' => date('Y') + 5));
	$formulaire->addElement('date'  , 'date_fin_appel_projet', 'Date de fin de l\'appel aux projets', array('language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5));
	$formulaire->addElement('date'  , 'date_fin_appel_conferencier', 'Date de fin de l\'appel aux conférenciers', array('language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5));
	$formulaire->addElement('checkbox', 'vote_enabled', 'Activer le vote sur les conférences');
    $formulaire->addElement('date'  , 'date_fin_vote', 'Date de fin de vote sur le CFP', array('language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5));
	$formulaire->addElement('date'  , 'date_fin_prevente'    , 'Date de fin de pré-vente'           , array('language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5));
	$formulaire->addElement('date'  , 'date_fin_vente'       , 'Date de fin de vente'               , array('language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5));
    $formulaire->addElement('date'  , 'date_fin_saisie_repas_speakers'       , 'Date de fin saisie repas confférenciers'               , array('language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5));
    $formulaire->addElement('date'  , 'date_fin_saisie_nuites_hotel'       , 'Date de fin saisie nuités hotel'               , array('language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5));
    $formulaire->addElement('date'  , 'date_annonce_planning'       , 'Date annonce planning'               , array('language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5));
	$formulaire->addElement('textarea', 'cfp_fr'             , 'CFP (fr)'                           , ['rows' => 5, 'cols' => 50, 'class' => 'simplemde']);
	$formulaire->addElement('textarea', 'cfp_en'             , 'CFP (en)'                           , ['rows' => 5, 'cols' => 50, 'class' => 'simplemde']);
    $formulaire->addElement('textarea', 'speaker_management_fr'             , 'Infos speakers (fr)'                           , ['rows' => 5, 'cols' => 50, 'class' => 'tinymce']);
    $formulaire->addElement('textarea', 'speaker_management_en'             , 'Infos speakers (eb)'                           , ['rows' => 5, 'cols' => 50, 'class' => 'tinymce']);
    $formulaire->addElement('textarea', 'sponsor_management_fr'             , 'Infos sponsors (fr)'                           , ['rows' => 5, 'cols' => 50, 'class' => 'tinymce']);
    $formulaire->addElement('textarea', 'sponsor_management_en'             , 'Infos sponsors (en)'                           , ['rows' => 5, 'cols' => 50, 'class' => 'tinymce']);
    $formulaire->addElement('textarea', 'mail_inscription_content'          , 'Contenu mail inscription'                      , ['rows' => 5, 'cols' => 50, 'class' => 'simplemde']);
    $formulaire->addElement('textarea', 'become_sponsor_description'        , "Contenu page devenir sponsor"                  , ['rows' => 5, 'cols' => 50, 'class' => 'simplemde']);
    $formulaire->addElement('checkbox', 'speakers_diner_enabled', "Activer le repas des speakers");
    $formulaire->addElement('checkbox', 'accomodation_enabled', "Activer les nuits d'hôtel");

    $formulaire->addElement('header', ''                     , 'Coupons');
    $legend = "Ici c'est une liste de coupons séparées par des virgules";
    $formulaire->addElement('textarea', 'coupons'             , 'Liste des coupons'                 , array( 'title' => $legend, 'placeholder' => $legend,'rows' => 5,'cols' => 50));
    $formulaire->addElement('submit'  , 'soumettre'   , 'Soumettre');

    $formulaire->addRule('titre' , 'Titre du forum manquant' , 'required');
    $formulaire->addRule('nb_places' , 'Nombre de places manquant' , 'required');

    if ($formulaire->validate()) {
        $valeurs = $formulaire->exportValues();
        if ($action == 'ajouter') {
            $ok = $forums->ajouter(
                $formulaire->exportValue('titre'),
                $formulaire->exportValue('nb_places'),
                $formulaire->exportValue('date_debut'),
                $formulaire->exportValue('date_fin'),
                $formulaire->exportValue('date_fin_appel_projet'),
                $formulaire->exportValue('date_fin_appel_conferencier'),
                $formulaire->exportValue('date_fin_vote'),
                $formulaire->exportValue('date_fin_prevente'),
                $formulaire->exportValue('date_fin_vente'),
                $formulaire->exportValue('date_fin_saisie_repas_speakers'),
                $formulaire->exportValue('date_fin_saisie_nuites_hotel'),
                $formulaire->exportValue('date_annonce_planning'),
                $formulaire->exportValue('path'),
                [
                    'fr' => $formulaire->exportValue('cfp_fr'),
                    'en' => $formulaire->exportValue('cfp_en'),
                    'speaker_management_fr' => $formulaire->exportValue('speaker_management_fr'),
                    'speaker_management_en' => $formulaire->exportValue('speaker_management_en'),
                    'sponsor_management_fr' => $formulaire->exportValue('sponsor_management_fr'),
                    'sponsor_management_en' => $formulaire->exportValue('sponsor_management_en'),
                    'mail_inscription_content' => $formulaire->exportValue('mail_inscription_content'),
                    'become_sponsor_description' => $formulaire->exportValue('become_sponsor_description'),
                ],
                $formulaire->exportValue('trello_list_id'),
                $formulaire->exportValue('logo_url'),
                $formulaire->exportValue('place_name'),
                $formulaire->exportValue('place_address'),
                $formulaire->exportValue('vote_enabled'),
                $formulaire->exportValue('speakers_diner_enabled'),
                $formulaire->exportValue('accomodation_enabled')
            );
            $id_forum = $forums->obtenirDernier();
        } else {
            $id_forum = $_GET['id'];
            $ok = $forums->modifier(
                $formulaire->exportValue('id'),
                $formulaire->exportValue('titre'),
                $formulaire->exportValue('nb_places'),
                $formulaire->exportValue('date_debut'),
                $formulaire->exportValue('date_fin'),
                $formulaire->exportValue('date_fin_appel_projet'),
                $formulaire->exportValue('date_fin_appel_conferencier'),
                $formulaire->exportValue('date_fin_vote'),
                $formulaire->exportValue('date_fin_prevente'),
                $formulaire->exportValue('date_fin_vente'),
                $formulaire->exportValue('date_fin_saisie_repas_speakers'),
                $formulaire->exportValue('date_fin_saisie_nuites_hotel'),
                $formulaire->exportValue('date_annonce_planning'),
                $formulaire->exportValue('path'),
                [
                    'fr' => $formulaire->exportValue('cfp_fr'),
                    'en' => $formulaire->exportValue('cfp_en'),
                    'speaker_management_fr' => $formulaire->exportValue('speaker_management_fr'),
                    'speaker_management_en' => $formulaire->exportValue('speaker_management_en'),
                    'sponsor_management_fr' => $formulaire->exportValue('sponsor_management_fr'),
                    'sponsor_management_en' => $formulaire->exportValue('sponsor_management_en'),
                    'mail_inscription_content' => $formulaire->exportValue('mail_inscription_content'),
                    'become_sponsor_description' => $formulaire->exportValue('become_sponsor_description'),
                ],
                $formulaire->exportValue('trello_list_id'),
                $formulaire->exportValue('logo_url'),
                $formulaire->exportValue('place_name'),
                $formulaire->exportValue('place_address'),
                $formulaire->exportValue('vote_enabled'),
                $formulaire->exportValue('speakers_diner_enabled'),
                $formulaire->exportValue('accomodation_enabled')
            );
        }

        $coupons->supprimerParForum($id_forum);
        $couponsPost = explode(',',$formulaire->exportValue('coupons'));
        foreach ($couponsPost as $c) {
            $c = trim($c);
            $coupons->ajouter($id_forum, $c);
        }

        if ($ok) {
            if ($action == 'ajouter') {
                Logs::log('Ajout du forum ' . $formulaire->exportValue('titre'));
            } else {
                Logs::log('Modification du forum ' . $formulaire->exportValue('titre') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('Le forum a été ' . (($action == 'ajouter') ? 'ajouté' : 'modifié'), 'index.php?page=forum_gestion&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' du forum');
        }
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));
    $smarty->assign('id_forum', $_GET['id']);
}
