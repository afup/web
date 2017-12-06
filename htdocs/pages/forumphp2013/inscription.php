<?php

use Afup\Site\Forum\Inscriptions;
use Afup\Site\Forum\Forum;
use Afup\Site\Forum\Facturation;
use Afup\Site\Utils\Pays;

require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';

$smarty->display('inscriptions_fermes.html');
