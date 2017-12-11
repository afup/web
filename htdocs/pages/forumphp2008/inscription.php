<?php

use Afup\Site\Forum\Inscriptions;
use Afup\Site\Forum\Forum;
use Afup\Site\Forum\Facturation;
use Afup\Site\Utils\Pays;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';
$smarty->display('inscriptions_fermes.html');
?>
