<?php

use Afup\Site\Planete\Flux;
use Afup\Site\Planete\Planete_Billet;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';



$billet                  = new Planete_Billet($bdd);
$flux                    = new Flux($bdd);
$page                    = isset($_GET['page']) ? abs((int)$_GET['page']) : 0;
$derniersBilletsComplets = $billet->obtenirDerniersBilletsTronques($page);
$listeFlux               = $flux->obtenirTousParDateDuDernierBillet();

$sous_site = 'planete';

$smarty->template_dir  = array(
    AFUP_CHEMIN_RACINE . 'templates/' . $sous_site . '/',
    AFUP_CHEMIN_RACINE . 'templates/commun/',
);
$smarty->assign('chemin_template',  '/');


$smarty->assign('billets',   $derniersBilletsComplets);
$smarty->assign('flux',      $listeFlux);
$smarty->assign('suivant',   count($derniersBilletsComplets) ? $page + 1 : -1);
$smarty->assign('precedant', $page - 1);

$smarty->display('index.html');

