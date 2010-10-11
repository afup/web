<?php
/**
 * Fichier principal site 'Annuaire'
 * 
 * @author    Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @author    Perrick Penet    <perrick@noparking.fr>
 * @author    Olivier Hoareau  <olivier@phppro.fr>
 * @copyright 2010 Association Française des Utilisateurs de PHP
 * 
 * @category Annuaire
 * @package  Annuaire
 * @group    Pages
 */

// 0. initialisation (bootstrap) de l'application


// 1. chargement des classes nécessaires

require '../../library/Afup.php';
require_once dirname(__FILE__) . '/../../../dependencies/smarty/Smarty.class.php';
require dirname(__FILE__) . '/../../../configs/application/config.php';

if ($_SERVER["SERVER_NAME"] === 'localhost') {
    $dsn  = 'mysql:dbname=afup;host=localhost';
    $user = 'root';
    $pass = 'mysql';
    $dbEncoding = 'utf8';
} else {
    $dsn  = 'mysql:dbname=' . $configuration['bdd']['base'] . ';host=' . $configuration['bdd']['hote'];
    $user = $configuration['bdd']['utilisateur'];
    $pass = $configuration['bdd']['mot_de_passe'];
    $dbEncoding = 'utf8';
}

header('Content-type: text/html; charset=UTF-8');

try {
    Afup_Directory_Controller::getInstance($dsn, $user, $pass, $dbEncoding)->dispatch();
} catch (PDOException $e) {
    echo $e->getMessage();
}