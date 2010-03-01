<?php

require_once dirname(__FILE__) . '/../../../../sources/Afup/Bootstrap/Cli.php';

require_once 'afup/AFUP_Site.php';

$site = new AFUP_Site();
$site->importer_spip();