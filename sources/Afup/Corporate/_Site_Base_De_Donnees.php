<?php

declare(strict_types=1);
namespace Afup\Site\Corporate;

use Afup\Site\Utils\Base_De_Donnees;
use Afup\Site\Utils\Configuration;

class _Site_Base_De_Donnees extends Base_De_Donnees
{
    public function __construct()
    {
        /**
         * @var Configuration $conf
         */
        $conf = $GLOBALS['AFUP_CONF'];
        parent::__construct($conf->obtenir('database_host'),
            $conf->obtenir('database_name'),
            $conf->obtenir('database_user'),
            $conf->obtenir('database_password'),
            $conf->obtenir('database_port')
        );
    }
}
