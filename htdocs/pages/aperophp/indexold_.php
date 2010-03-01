<?php
/**
 * Fichier principal site 'AperoPHP'
 * 
 * @author    Perrick Penet   <perrick@noparking.fr>
 * @author    Olivier Hoareau <olivier@phppro.fr>
 * @copyright 2010 Association Française des Utilisateurs de PHP
 * 
 * @category AperoPHP
 * @package  AperoPHP
 * @group    Pages
 */

// 0. initialisation (bootstrap) de l'application

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

// 1. chargement des classes nécessaires

require_once 'Afup/AFUP_Aperos.php';

// 2. récupération et filtrage des données

$aperos     = new AFUP_Aperos($bdd);
$evenements = $aperos->obtenirListe();

// 3. assignations des variables du template

$smarty->assign('evenements', $evenements);

// 4. affichage de la page en utilisant le modèle spécifié

$smarty->display('index.html');