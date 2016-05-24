<?php
namespace Afup\Site\Corporate\Lille;

use Afup\Site\Corporate\Article;

class _Article_Lille extends Article
{
    function afficher()
    {
        return '<div id="article-ariane">' . $this->fil_d_ariane("pages/lille/") . "</div>" .
        '<h1>' . $this->titre() . '</h1>' .
        $this->corps() .
        '<div class="article-date">' . $this->date() . '</div>';
    }
}