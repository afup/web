<?php

header('Content-type: text/html; charset=UTF-8');

/**
 * Annuaire des prestataires AFUP
 * 
 * Conventions de codage ? respecter : 
 * http://framework.zend.com/manual/fr/coding-standard.html
 * 
 * Editez ce projet en UTF-8
 *
 * @author Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @version 1.0
 * @copyright 2006 Association Française des Utilisateurs de PHP
 * @package afup
 * @subpackage directory
 * @since 1.0 - Fri Jun 02 18:10:09 CEST 2006
 */
require '../../library/Afup.php';
require '../../include/configuration.inc.php';

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

try {
    Afup_Directory_Controller::getInstance($dsn, $user, $pass, $dbEncoding)->dispatch();
} catch (PDOException $e) {
    echo $e->getMessage();
}
