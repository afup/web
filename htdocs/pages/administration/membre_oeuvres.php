<?php

$action = verifierAction(array('ausculter', 'calculer'));
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Personnes_Physiques.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Planete_Billet.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Oeuvres.php';
$oeuvres = new AFUP_Oeuvres($bdd);

if ($action == 'calculer') {
    if ($oeuvres->calculer()) {
        AFUP_Logs::log('Calculer les oeuvres de l\'AFUP');
        afficherMessage('Les oeuvres ont été calculées', 'index.php?page=membre_oeuvres');
    } else {
        afficherMessage('Une erreur est survenue lors du calcul des oeuvres', 'index.php?page=membre_oeuvres');
    }
}
$id_personne_physique = isset($_GET['id_personne_physique']) ? (int)$_GET['id_personne_physique'] : $droits->obtenirIdentifiant();
$mes_sparklines = $oeuvres->obtenirSparklinePersonnelleSur12Mois($id_personne_physique);
$smarty->assign('mes_sparklines', $mes_sparklines);

$id_personnes_physiques = $oeuvres->obtenirPersonnesPhysiquesLesPlusActives();
$les_sparklines = $oeuvres->obtenirSparklinesParCategorieDes12DerniersMois($id_personnes_physiques);
$smarty->assign('les_sparklines_actives', $les_sparklines);

$persone_physique = new AFUP_Personnes_Physiques($bdd);
$les_personnes_physiques = $persone_physique->obtenirListe('*', 'nom, prenom', false, false, true, $id_personnes_physiques);
$smarty->assign('les_personnes_physiques', $les_personnes_physiques);
