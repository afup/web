<?php
use Afup\Site\Forum\Inscriptions;

require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';


$inscriptions = new Inscriptions($bdd);
$inscriptions->modifierEtatInscription($_GET['cmd'], AFUP_FORUM_ETAT_REFUSE);

$smarty->display('paybox_refuse.html');
?>