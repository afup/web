<?php

// Impossible to access the file itself
use Afup\Site\Association\Personnes_Physiques;
use Afup\Site\Association\Personnes_Morales;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}


$personnes_physiques = new Personnes_Physiques($bdd);
$smarty->assign('membres_actifs'  , $personnes_physiques->obtenirNombreMembres(AFUP_DROITS_ETAT_ACTIF));
$smarty->assign('membres_inactifs', $personnes_physiques->obtenirNombreMembres(AFUP_DROITS_ETAT_INACTIF));
$smarty->assign('membres_total'   , $personnes_physiques->obtenirNombreMembres());

$smarty->assign('personnes_physiques_actives'  , $personnes_physiques->obtenirNombrePersonnesPhysiques(AFUP_DROITS_ETAT_ACTIF));
$smarty->assign('personnes_physiques_inactives', $personnes_physiques->obtenirNombrePersonnesPhysiques(AFUP_DROITS_ETAT_INACTIF));
$smarty->assign('personnes_physiques_total'    , $personnes_physiques->obtenirNombrePersonnesPhysiques());


$personnes_morales = new Personnes_Morales($bdd);
$smarty->assign('personnes_morales_actives'  , $personnes_morales->obtenirNombrePersonnesMorales(AFUP_DROITS_ETAT_ACTIF));
$smarty->assign('personnes_morales_inactives', $personnes_morales->obtenirNombrePersonnesMorales(AFUP_DROITS_ETAT_INACTIF));
$smarty->assign('personnes_morales_total'    , $personnes_morales->obtenirNombrePersonnesMorales());

?>