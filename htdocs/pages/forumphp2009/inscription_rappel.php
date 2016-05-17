<?php
use Afup\Site\Forum\Inscriptions;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';
require_once dirname(__FILE__) . '/_config.inc.php';

$inscriptions = new Inscriptions($bdd);

$inscriptions->ajouterRappel($_POST['email']);
$smarty->display('inscriptions_rappel.html');
?>