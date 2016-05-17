<?php
namespace Afup\Site\Corporate\Lille;


use Afup\Site\Corporate\Branche;
use Afup\Site\Corporate\Page;

class _Page_Lille extends Page
{
    function header()
    {
        $header = new _Header_Lille();
        $header->setTitle($this->title);
        return $header->render();
    }

    function footer()
    {
        $footer = new _Footer_Lille();
        return $footer->render();
    }

    function menu()
    {
        $branche = new Branche($this->bdd);

        return '<div id="header-menu">' .
        'Pour en savoir plus sur l\'antenne de lille' .
        $branche->naviguer(36, 2, "header-menu-local", "pages/lille/") .
        'Et pendant ce temps-là, l\'association national...' .
        $branche->naviguer(5, 2, "header-menu-local") .
        '</div>';
    }

    function definirRoute($route)
    {
        $this->route = $route;
        switch (true) {
            case preg_match("%\s*/[0-9]*/\s*%", $this->route):
                list(, $id,) = explode("/", $this->route);
                $article = new _Article_Lille($id, $this->bdd);
                $article->charger();
                $this->title = $article->titre;
                $this->content = $article->afficher();
                break;

            case preg_match("%s*/[0-9]*%", $this->route):
                list(, $id) = explode("/", $this->route);
                $rubrique = new _Rubrique_Lille($id, $this->bdd);
                $rubrique->charger();
                $this->title = $rubrique->nom;
                $this->content = $rubrique->afficher();
                break;
        }
    }

    function content()
    {
        return $this->content;
    }
}