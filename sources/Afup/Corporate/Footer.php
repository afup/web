<?php

declare(strict_types=1);
namespace Afup\Site\Corporate;

class Footer
{
    public function logos(): string
    {
        $branche = new Branche();
        $branche->navigation_avec_image(true);
        return '<ul id="LogoBar"><li>' . $branche->naviguer(9, 0, "LogoElement") . '</li></ul>';
    }

    public function questions(): string
    {
        $articles = new Articles();
        $questions = $articles->chargerDernieresQuestions();

        $contenu = '<ul>';
        foreach ($questions as $question) {
            $contenu .= '<li><a href="' . $question->route() . '">' . $question->titre() . '</a></li>';
        }

        return $contenu . '</ul>';
    }

    public function articles(): string
    {
        $articles = new Articles();
        $ajouts = $articles->chargerDerniersAjouts();
        $contenu = '<ul>';
        foreach ($ajouts as $ajout) {
            $contenu .= '<li><a href="' . $ajout->route() . '">' . $ajout->titre() . '</a></li>';
        }

        return $contenu . '</ul>';
    }

    public function render(): string
    {
        return "";
    }
}
