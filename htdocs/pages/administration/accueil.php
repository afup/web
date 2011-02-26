<?php

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Personnes_Physiques.php';
$personnes_physiques = new AFUP_Personnes_Physiques($bdd);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Pays.php';
$pays = new AFUP_Pays($bdd);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Utils.php';

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Tags.php';
$tags = new AFUP_Tags($bdd);


$membre = $personnes_physiques->obtenir($droits->obtenirIdentifiant());
unset($membre['mot_de_passe']);

// Obtention du gravatar
$membre["gravatar"] = AFUP_Utils::get_gravatar($membre["email"]);
$membre["tags"] = $tags->obtenirTagsSurPersonnePhysique($membre["id"]);
 
//var_dump($membre);die;
$smarty->assign('membre', $membre);
$smarty->assign('pays',$pays->obtenirPays());
?>