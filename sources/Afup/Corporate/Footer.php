<?php
namespace Afup\Site\Corporate;

class Footer
{
    private $conf;

    function __construct()
    {
        $this->conf = $GLOBALS['AFUP_CONF'];
    }

    function logos()
    {
        $branche = new Branche();
        $branche->navigation_avec_image(true);
        return '<ul id="LogoBar"><li>' . $branche->naviguer(9, 0, "LogoElement") . '</li></ul>';
    }

    function questions()
    {
        $articles = new Articles();
        $questions = $articles->chargerDernieresQuestions();

        $contenu = '<ul>';
        foreach ($questions as $question) {
            $contenu .= '<li><a href="' . $question->route() . '">' . $question->titre() . '</a></li>';
        }
        $contenu .= '</ul>';

        return $contenu;
    }

    function articles()
    {
        $articles = new Articles();
        $ajouts = $articles->chargerDerniersAjouts();
        $contenu = '<ul>';
        foreach ($ajouts as $ajout) {
            $contenu .= '<li><a href="' . $ajout->route() . '">' . $ajout->titre() . '</a></li>';
        }
        $contenu .= '</ul>';

        return $contenu;
    }

    function render()
    {
        return "";
    }
}