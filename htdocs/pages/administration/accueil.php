<?php

// Impossible to access the file itself
use Afup\Site\Tags;
use Afup\Site\Association\Personnes_Physiques;
use Afup\Site\Utils\Utils;
use Afup\Site\Utils\Pays;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$personnes_physiques = new Personnes_Physiques($bdd);

$pays = new Pays($bdd);

$tags = new Tags($bdd);


$membre = $personnes_physiques->obtenir($droits->obtenirIdentifiant());
unset($membre['mot_de_passe']);

// Obtention du gravatar
$membre["gravatar"] = Utils::get_gravatar($membre["email"]);
$membre["tags"] = $tags->obtenirTagsSurPersonnePhysique($membre["id"]);
 
//var_dump($membre);die;
$smarty->assign('membre', $membre);
$smarty->assign('pays',$pays->obtenirPays());
?>