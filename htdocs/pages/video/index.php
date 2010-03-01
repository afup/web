<?php
/**
 * Fichier principal site 'Video'
 * 
 * @author    Perrick Penet   <perrick@noparking.fr>
 * @author    Olivier Hoareau <olivier@phppro.fr>
 * @copyright 2010 Association Française des Utilisateurs de PHP
 * 
 * @category Video
 * @package  Video
 * @group    Pages
 */

// 0. initialisation (bootstrap) de l'application

require_once dirname(__FILE__) . '/../../include/prepend.inc.php';

// 1. chargement des classes nécessaires


// 2. récupération et filtrage des données

$videos = array();

$videos[0] = array('/pages/video/avphp_little.flv', 330, 250, "Que pensent-ils de PHP ?");
$videos[1] = array('/pages/video/avphp_normal.flv', 410, 310, "Que pensent-ils de PHP ?");

//$videos[2] = array('/pages/video/avphp_high.flv',   570, 430, "Que pensent-ils de PHP ?");

$videos[3] = array('/pages/video/itw_little.flv',   330, 250, "Qui sont-ils, que vous conseillent-ils ?");

//$videos[4] = array('/pages/video/itw_normal.flv',   410, 310, "Qui sont-ils, que vous conseillent-ils ?");
//$videos[5] = array('/pages/video/itw_high.flv',     570, 430, "Qui sont-ils, que vous conseillent-ils ?");

if (!isset($videos[$_SERVER['QUERY_STRING']])) {
    $title = 'Pas de vidÃ©o';
    $video = null;
} else {
    $title = $videos[$_SERVER['QUERY_STRING']][3];
    $video = $videos[$_SERVER['QUERY_STRING']];
}

header('Content-type: text/html; charset=UTF-8');
include 'video.php';