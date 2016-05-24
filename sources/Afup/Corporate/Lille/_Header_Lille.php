<?php
namespace Afup\Site\Corporate\Lille;

use Afup\Site\Corporate\Header;
use Afup\Site\Utils\Configuration;

class _Header_Lille extends Header
{
    function __construct()
    {
        $this->setTitle('Lille');
        $this->addCSS('templates/lille/medias/css/lille.css');
        $this->javascript = '';
        $this->addRSS();
    }

    function setTitle($string)
    {
        $this->title = '<title>' . $string . ' | Antenne local de l\'AFUP : Lille</title>';
    }

    function addRSS()
    {
        /**
         * @var $conf Configuration
         */
        $conf = $GLOBALS['AFUP_CONF'];
        $rssFile = $conf->obtenir('web|path') . '/pages/lille/rss.php';
        $this->rss = '<link rel="alternate" type="application/rss+xml" href="' . $rssFile . '" title="Derni&egraves actus de l\'AFUP Lille"/>';
    }
}