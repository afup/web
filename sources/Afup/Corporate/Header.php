<?php
namespace Afup\Site\Corporate;

class Header
{
    public $title;
    public $css = '';
    public $javascript;
    public $rss;

    function __construct()
    {
        $this->setTitle('promouvoir le PHP aupr&egrave;s des professionnels');
        $this->addCSS('templates/site/css/site.css');
        $this->javascript = '';
        $this->addRSS();
    }

    function setTitle($string)
    {
        $this->title = '<title>' . $string . ' | Association Fran&ccedil;aise des Utilisateurs de PHP (afup.org)</title>';
    }

    function addCSS($file)
    {
        $conf = $GLOBALS['AFUP_CONF'];
        var_dump($file);
        die();
        $file = $conf->obtenir('web|path') . $file;
        $this->css .= '<link rel="stylesheet" href="' . $file . '" type="text/css" media="all" />';
    }

    function addRSS()
    {
        $conf = $GLOBALS['AFUP_CONF'];
        $rssFile = $conf->obtenir('web|path') . $conf->obtenir('site|prefix') . 'rss.php';
        $this->rss = '<link rel="alternate" type="application/rss+xml" href="' . $rssFile . '" title="Derni&egraves actus de l\'AFUP"/>';
    }

    function render()
    {
        return $this->title . $this->css . $this->rss . $this->javascript;
    }
}