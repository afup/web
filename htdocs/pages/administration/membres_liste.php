<?php

// Impossible to access the file itself
use Afup\Site\Tags;
use Afup\Site\Association\Personnes_Physiques;
use Afup\Site\Association\Personnes_Morales;
use Afup\Site\Utils\Utils;
use Afup\Site\Utils\Pays;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('lister', 'detail', 'rechercher'));
$smarty->assign('action', $action);

//
//$tags = new AFUP_Liste_Membres($bdd);

$personnes_physiques = new Personnes_Physiques($bdd);
$pays = new Pays($bdd);
$personnes_morales = new Personnes_Morales($bdd);
$tags = new Tags($bdd);

    $list_champs = '*';
    $list_ordre = 'nom, prenom';
    $list_sens = 'asc';
    $list_filtre = (isset($_POST["nom"])? $_POST["nom"] : false);
    $is_active = 1;
    
    // Obtention du gravatar
    $personnes_physiques_liste = $personnes_physiques->obtenirListe($list_champs, $list_ordre, $list_filtre, false, false, false, $is_active);
    foreach($personnes_physiques_liste as &$personne_physique) {
    	$personne_physique["gravatar"] = Utils::get_gravatar($personne_physique["email"]);
    	$personne_physique["tags"] = $tags->obtenirTagsSurPersonnePhysique($personne_physique["id"]);
    }
   // var_dump($personnes_physiques_liste);die;
    $smarty->assign('membres', $personnes_physiques_liste);
    $smarty->assign('entreprises', $personnes_morales->obtenirListe('id, raison_sociale', 'raison_sociale', true));
    $smarty->assign('pays', $pays->obtenirPays());

    
    $formulaire = &instancierFormulaire();

	$formulaire->addElement('header'  , ''         , 'Rechercher un membre');
	
	$formulaire->addElement('static',   'note'     , ' '                , 'Tapez le nom ou la ville d\'un membre.');
	$formulaire->addElement('text'    , 'nom'      , 'Nom'           , array('size' => 40, 'maxlength' => 40));
	
	$formulaire->addElement('header'  , 'boutons'  , '');
	$formulaire->addElement('submit'  , 'soumettre', 'Rechercher');
	
	$formulaire->addRule('nom'      , 'Nom manquant'    , 'required');	
	
	$smarty->assign('formulaire', genererFormulaire($formulaire));
