<?php

// Impossible to access the file itself
use Afup\Site\Forum\Coupon;
use Afup\Site\Forum\Forum;
use Afup\Site\Utils\Logs;

/** @var \AppBundle\Controller\LegacyController $this */
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(['ajouter', 'modifier']);
$smarty->assign('action', $action);


$forums = new Forum($bdd);
$coupons = new Coupon($bdd);
$forumPath = null;

$formulaire = instancierFormulaire();
if ($action == 'ajouter') {
    $formulaire->setDefaults([
        'civilite' => 'M.',
        'id_pays' => 'FR'
    ]);
} else {
    $champs = $forums->obtenir($_GET['id']);
    $champs['coupons'] = implode(', ', $coupons->obtenirCouponsForum($_GET['id']));
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

    $forumPath = $champs['path'];

    $formulaire->setDefaults($champs);

    if (isset($champs) && isset($champs['id'])) {
        $_GET['id'] = $champs['id'];
    }

    $formulaire->addElement('hidden', 'id', $_GET['id']);
}

$formulaire->addElement('header', '', "Gestion d'événement");
$formulaire->addElement('text', 'titre', "Titre de l'événement", ['size' => 30, 'maxlength' => 100]);
$formulaire->addElement('text', 'path', 'Chemin du template', ['size' => 30, 'maxlength' => 100]);
$formulaire->addElement('static', 'info', '',
    '<i>Le path sert également à déterminer le nom du template de mail à utiliser sur mandrill, sous la forme confirmation-inscription-{PATH}</i>');
$formulaire->addElement('text', 'trello_list_id', 'Liste trello pour les leads',
    ['size' => 30, 'maxlength' => 100]);
$formulaire->addElement('text', 'logo_url', "URL du logo de l'événement", ['size' => 30, 'maxlength' => 255]);
$formulaire->addElement('text', 'nb_places', 'Nombre de places', ['size' => 30, 'maxlength' => 100]);
$formulaire->addElement('text', 'place_name', 'Nom du lieu', ['size' => 30, 'maxlength' => 255]);
$formulaire->addElement('text', 'place_address', 'Adresse du lieu', ['size' => 30, 'maxlength' => 255]);
$formulaire->addElement('date', 'date_debut', 'Date de début',
    ['language' => 'fr', 'format' => "dMY", 'minYear' => 2001, 'maxYear' => date('Y') + 5]);
$formulaire->addElement('date', 'date_fin', 'Date de fin',
    ['language' => 'fr', 'format' => "dMY", 'minYear' => 2001, 'maxYear' => date('Y') + 5]);
$formulaire->addElement('date', 'date_fin_appel_projet', 'Date de fin de l\'appel aux projets',
    ['language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5]);
$formulaire->addElement('date', 'date_fin_appel_conferencier', 'Date de fin de l\'appel aux conférenciers',
    ['language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5]);
$formulaire->addElement('checkbox', 'vote_enabled', 'Activer le vote sur les conférences');
$formulaire->addElement('date', 'date_fin_vote', 'Date de fin de vote sur le CFP',
    ['language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5]);
$formulaire->addElement('date', 'date_fin_prevente', 'Date de fin de pré-vente',
    ['language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5]);
$formulaire->addElement('date', 'date_fin_vente', 'Date de fin de vente',
    ['language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5]);
$formulaire->addElement('date', 'date_fin_saisie_repas_speakers', 'Date de fin saisie repas confférenciers',
    ['language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5]);
$formulaire->addElement('date', 'date_fin_saisie_nuites_hotel', 'Date de fin saisie nuités hotel',
    ['language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5]);
$formulaire->addElement('date', 'date_annonce_planning', 'Date annonce planning',
    ['language' => 'fr', 'format' => "dMYH:i:s", 'minYear' => 2001, 'maxYear' => date('Y') + 5]);
$formulaire->addElement('text', 'waiting_list_url', "URL de la liste d'attente",
    ['size' => 30, 'maxlength' => 255]);
$formulaire->addElement('textarea', 'cfp_fr', 'CFP (fr)', ['rows' => 5, 'cols' => 50, 'class' => 'simplemde']);
$formulaire->addElement('textarea', 'cfp_en', 'CFP (en)', ['rows' => 5, 'cols' => 50, 'class' => 'simplemde']);
$formulaire->addElement('textarea', 'speaker_management_fr', 'Infos speakers (fr)',
    ['rows' => 5, 'cols' => 50, 'class' => 'tinymce']);
$formulaire->addElement('textarea', 'speaker_management_en', 'Infos speakers (eb)',
    ['rows' => 5, 'cols' => 50, 'class' => 'tinymce']);
$formulaire->addElement('textarea', 'sponsor_management_fr', 'Infos sponsors (fr)',
    ['rows' => 5, 'cols' => 50, 'class' => 'tinymce']);
$formulaire->addElement('textarea', 'sponsor_management_en', 'Infos sponsors (en)',
    ['rows' => 5, 'cols' => 50, 'class' => 'tinymce']);
$formulaire->addElement('textarea', 'mail_inscription_content', 'Contenu mail inscription',
    ['rows' => 5, 'cols' => 50, 'class' => 'simplemde']);
$formulaire->addElement('textarea', 'become_sponsor_description', "Contenu page devenir sponsor",
    ['rows' => 5, 'cols' => 50, 'class' => 'simplemde']);
$formulaire->addElement('checkbox', 'speakers_diner_enabled', "Activer le repas des speakers");
$formulaire->addElement('checkbox', 'accomodation_enabled', "Activer les nuits d'hôtel");

$formulaire->addElement('header', '', 'Coupons');
$legend = "Ici c'est une liste de coupons séparées par des virgules";
$formulaire->addElement('textarea', 'coupons', 'Liste des coupons',
    ['title' => $legend, 'placeholder' => $legend, 'rows' => 5, 'cols' => 50]);
$formulaire->addElement('submit', 'soumettre', 'Soumettre');

$formulaire->addRule('titre', 'Titre du forum manquant', 'required');
$formulaire->addRule('nb_places', 'Nombre de places manquant', 'required');

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
            $formulaire->exportValue('accomodation_enabled'),
            $formulaire->exportValue('waiting_list_url')

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
            $formulaire->exportValue('accomodation_enabled'),
            $formulaire->exportValue('waiting_list_url')
        );
    }

    $coupons->supprimerParForum($id_forum);
    $couponsPost = explode(',', $formulaire->exportValue('coupons'));
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
        afficherMessage('Le forum a été ' . (($action == 'ajouter') ? 'ajouté' : 'modifié'), '/admin/event/list');
    } else {
        $smarty->assign('erreur',
            'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' du forum');
    }
}

$smarty->assign('formulaire', genererFormulaire($formulaire));
$smarty->assign('id_forum', $_GET['id']);
$smarty->assign('forum_path', $forumPath);
