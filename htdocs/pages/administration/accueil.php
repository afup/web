<?php

// Impossible to access the file itself
use Afup\Site\Association\Personnes_Physiques;
use Afup\Site\Utils\Utils;
use Afup\Site\Utils\Pays;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$personnes_physiques = new Personnes_Physiques($bdd);

$pays = new Pays($bdd);

$membre = $personnes_physiques->obtenir($droits->obtenirIdentifiant());
unset($membre['mot_de_passe']);

// Obtention du gravatar
$membre["gravatar"] = Utils::get_gravatar($membre["email"]);

$officesCollection = new \AppBundle\Offices\OfficesCollection();

try {
    $nearestOffice = $officesCollection->findByCode($membre['nearest_office']);
} catch (\InvalidArgumentException $e) {
    $nearestOffice = null;
}


$smarty->assign('membre', $membre);
$smarty->assign('pays',$pays->obtenirPays());
$smarty->assign('nearest_office', $nearestOffice);
?>
