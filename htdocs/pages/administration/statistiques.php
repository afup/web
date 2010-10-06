<?php
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Personnes_Physiques.php';
$personnes_physiques = new AFUP_Personnes_Physiques($bdd);
$smarty->assign('membres_actifs'  , $personnes_physiques->obtenirNombreMembres(AFUP_DROITS_ETAT_ACTIF));
$smarty->assign('membres_inactifs', $personnes_physiques->obtenirNombreMembres(AFUP_DROITS_ETAT_INACTIF));
$smarty->assign('membres_total'   , $personnes_physiques->obtenirNombreMembres());

$smarty->assign('personnes_physiques_actives'  , $personnes_physiques->obtenirNombrePersonnesPhysiques(AFUP_DROITS_ETAT_ACTIF));
$smarty->assign('personnes_physiques_inactives', $personnes_physiques->obtenirNombrePersonnesPhysiques(AFUP_DROITS_ETAT_INACTIF));
$smarty->assign('personnes_physiques_total'    , $personnes_physiques->obtenirNombrePersonnesPhysiques());

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Personnes_Morales.php';
$personnes_morales = new AFUP_Personnes_Morales($bdd);
$smarty->assign('personnes_morales_actives'  , $personnes_morales->obtenirNombrePersonnesMorales(AFUP_DROITS_ETAT_ACTIF));
$smarty->assign('personnes_morales_inactives', $personnes_morales->obtenirNombrePersonnesMorales(AFUP_DROITS_ETAT_INACTIF));
$smarty->assign('personnes_morales_total'    , $personnes_morales->obtenirNombrePersonnesMorales());

?>