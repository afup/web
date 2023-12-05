<?php
namespace Afup\Site\Corporate;

use Afup\Site\Utils\Base_De_Donnees;
use Afup\Site\Utils\Configuration;

class _Site_Base_De_Donnees extends Base_De_Donnees
{
    function __construct()
    {
        /**
         * @var Configuration $conf
         */
        $conf = $GLOBALS['AFUP_CONF'];
        parent::__construct($conf->obtenir('bdd|hote'),
            $conf->obtenir('bdd|base'),
            $conf->obtenir('bdd|utilisateur'),
            $conf->obtenir('bdd|mot_de_passe'));

    }
}
