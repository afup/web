<?php

// Impossible to access the file itself
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('modifier', 'supprimer', 'contempler'));
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Tags.php';
$tags = new AFUP_Tags($bdd);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Personnes_Physiques.php';
$personnes_physiques = new AFUP_Personnes_Physiques($bdd);

$smarty->assign('tags_utilises', $tags->obtenirListeUnique());

if (isset($_GET['tag'])) {
    $smarty->assign('tag_selectionne', $_GET['tag']);
    $smarty->assign('membres_tagues', $tags->obtenirPersonnesPhysisquesTagues($_GET['tag']));
}

if (isset($_GET['id_personne_physique'])) {
    $smarty->assign('id_personne_physique', $_GET['id_personne_physique']);
    $smarty->assign('id_personne_connectee', $droits->obtenirIdentifiant());
    $smarty->assign('membre', $personnes_physiques->obtenir($_GET['id_personne_physique']));
    $smarty->assign('tags_membre', $tags->obtenirTagsSurPersonnePhysique($_GET['id_personne_physique']));
    $id_source = $_GET['id_personne_physique'];
}

$liste_personnes_physiques = $personnes_physiques->obtenirListe('id, CONCAT(nom, " ", prenom)', 'nom, prenom', false, false, true);

$formulaire = &instancierFormulaire();
if (!isset($id_source)) {
    $id_source = $droits->obtenirIdentifiant();
}
$tagsMembre = $tags->obtenirTagsSurPersonnePhysique($id_source, 'id, tag', 'tag', true);
foreach ($tagsMembre as $k => $t) {
    $t = trim($t);
    if (!$t) {
        unset($tagsMembre[$k]);
    } else {
        if (str_word_count($t) > 1) {
            $tagsMembre[$k] = "'$t'";
        }
    }
}
$formulaire->setDefaults(array('id_source' => $id_source,
                               'tag'       => implode(' ', array_values($tagsMembre))));
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$formulaire->addElement('hidden', 'id'       , $id);
$formulaire->addElement('hidden', 'source'   , 'afup_personnes_physiques');

$formulaire->addElement('header'  , ''         , 'Taguer un membre');
$formulaire->addElement('static'  , 'note'     , ' '                , 'Pour inscrire plusieurs tags, des espaces suffisent.<br />Pour un tag de plusieurs mots, pensez aux guillemets simples.<br />Exemple complet : <em>blog tdd \'php mysql\' \'php oracle\'</em>');
$formulaire->addElement('textarea', 'tag'      , 'Tag(s)'           , array('rows' => 10, 'cols' => 50));
$formulaire->addElement('select'  , 'id_source', 'Membre'           , $liste_personnes_physiques);

$formulaire->addElement('header'  , 'boutons'  , '');
$formulaire->addElement('submit'  , 'soumettre', ucfirst($action));

$formulaire->addRule('tag'      , 'Tag manquant'    , 'required');
$formulaire->addRule('id_source', 'Membre manquante', 'required');

if ($formulaire->validate()) {

    // Suppression des tags existants
    if ($tags->supprimerParPersonnesPhysiques($droits->obtenirIdentifiant())) {
        AFUP_Logs::log('Suppression des tags de l\'utilisateur ' . $droits->obtenirIdentifiant());
    }

    // Enregsitrement des nouveaux tags
    $ok = $tags->enregistrerTags($formulaire, $droits->obtenirIdentifiant(), time());

    if ($ok) {
        AFUP_Logs::log('Enregistrement d\'un tag (' . $formulaire->exportValue('tag') . ')');
        afficherMessage('Le tag a été enregistré', 'index.php?page=membre_tags');
    } else {
        $smarty->assign('erreur', 'Une erreur est survenue lors de l\'enregistrement du tag');
    }
}

$smarty->assign('formulaire', genererFormulaire($formulaire));
