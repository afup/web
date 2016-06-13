<?php

// Impossible to access the file itself
use Afup\Site\Association\Personnes_Physiques;
use Afup\Site\Oeuvres;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('ausculter', 'calculer'));
$smarty->assign('action', $action);

$oeuvres = new Oeuvres($bdd);
$persone_physique = new Personnes_Physiques($bdd);

if ($action == 'calculer') {
    if ($oeuvres->calculer()) {
        Logs::log('Calculer les oeuvres de l\'AFUP');
        afficherMessage('Les oeuvres ont été calculées', 'index.php?page=membre_oeuvres');
    } else {
        afficherMessage('Une erreur est survenue lors du calcul des oeuvres', 'index.php?page=membre_oeuvres', true);
    }
}
$id_personne_physique = isset($_GET['id_personne_physique']) ? (int)$_GET['id_personne_physique'] : $droits->obtenirIdentifiant();
$mes_sparklines = $oeuvres->obtenirSparklinePersonnelleSur12Mois($id_personne_physique);
$smarty->assign('mes_sparklines', $mes_sparklines);

$categories = $oeuvres->obtenirCategories();
$les_personnes_physiques = array();
foreach($categories as $categorie) {
	$id_personnes_physiques = $oeuvres->obtenirPersonnesPhysiquesLesPlusActives($categorie);
	$les_sparklines = $oeuvres->obtenirSparklinesParCategorieDes12DerniersMois($id_personnes_physiques, $categorie);
	$smarty->assign('les_sparklines_actives_'.$categorie, $les_sparklines);
    $les_personnes_physiques += $persone_physique->obtenirListe('*', 'nom, prenom', false, false, true, $id_personnes_physiques);
}
$smarty->assign('les_personnes_physiques', $les_personnes_physiques);
