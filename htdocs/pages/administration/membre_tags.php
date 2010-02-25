<?php

$action = verifierAction(array('modifier', 'supprimer', 'contempler'));
$smarty->assign('action', $action);

require_once AFUP_CHEMIN_RACINE . 'classes/afup/AFUP_Tags.php';
$tags = new AFUP_Tags($bdd);

require_once AFUP_CHEMIN_RACINE . 'classes/afup/AFUP_Personnes_Physiques.php';
$personnes_physiques = new AFUP_Personnes_Physiques($bdd);

$smarty->assign('tags_utilises', $tags->obtenirListeUnique());

if ($action == 'supprimer') {
    $id_personne_physique = isset($_GET['id_personne_physique']) ? $_GET['id_personne_physique'] : 0;
    if ($tags->supprimer($_GET['id'])) {
        AFUP_Logs::log('Suppression du tag ' . $_GET['id']);
        afficherMessage('Le tag a été supprimé', 'index.php?page=membre_tags&id_personne_physique=' . $id_personne_physique);
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression du tag', 'index.php?page=membre_tags&id_personne_physique=' . $id_personne_physique, true);
    }
} elseif ($action == 'contempler') {
    
}

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
$formulaire->setDefaults(array('id_source' => $id_source));
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$formulaire->addElement('hidden', 'id'       , $id);
$formulaire->addElement('hidden', 'source'   , 'afup_personnes_physiques');

$formulaire->addElement('header'  , ''         , 'Taguer un membre');
$formulaire->addElement('static',   'note'     , ' '                , 'Pour inscrire plusieurs tags, des espaces suffisent.<br />Pour un tag de plusieurs mots, pensez aux guillemets simples.<br />Exemple complet : <em>blog tdd \'php mysql\' \'php oracle\'</em>');
$formulaire->addElement('text'    , 'tag'      , 'Tag(s)'           , array('size' => 40, 'maxlength' => 40));
$formulaire->addElement('select'  , 'id_source', 'Membre'           , $liste_personnes_physiques);

$formulaire->addElement('header'  , 'boutons'  , '');
$formulaire->addElement('submit'  , 'soumettre', ucfirst($action));

$formulaire->addRule('tag'      , 'Tag manquant'    , 'required');
$formulaire->addRule('id_source', 'Membre manquante', 'required');

if ($formulaire->validate()) {
    $ok = $tags->enregistrerTags($formulaire, $droits->obtenirIdentifiant(), time());

    if ($ok) {
        AFUP_Logs::log('Enregistrement d\'un tag (' . $formulaire->exportValue('tag') . ')');
        afficherMessage('Le tag a été enregistré', 'index.php?page=membre_tags');
    } else {
        $smarty->assign('erreur', 'Une erreur est survenue lors de l\'enregistrement du tag');
    }
}

$smarty->assign('formulaire', genererFormulaire($formulaire));
