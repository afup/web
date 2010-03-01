<?php

require_once dirname(__FILE__) . '/../Bootstrap/Cli.php';

require_once 'Afup/AFUP_Site.php';

$site = new AFUP_Site();
$site->importer_spip();
