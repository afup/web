<?php
use Afup\Site\Forum\Inscriptions;

require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';


$inscription_manager = new Inscriptions($bdd) ;
$md5_code = $_GET['id'] ; 
$inscrit = $inscription_manager->obtenirInscription($md5_code) ; 

if(empty($inscrit))
{
  header('Location: /pages/forumphp'.$config_forum['annee'].'/index.php') ; 
}

$smarty->assign('inscrit', $inscrit) ; 
$smarty->display('convocation_visiteurs.html') ; 