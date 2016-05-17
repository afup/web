<?php
namespace Afup\Site\Corporate;

use Afup\Site\Utils\Configuration;

class Page
{
    public $route = "";
    public $content;
    public $title;


    /**
     * @var Configuration
     */
    public $conf;

    function __construct($bdd = false)
    {
        if ($bdd) {
            $this->bdd = $bdd;
        } else {
            $this->bdd = new _Site_Base_De_Donnees();
        }
        $this->conf = $GLOBALS['AFUP_CONF'];
    }

    function definirRoute($route)
    {
        $this->route = $route;
        switch (true) {
            case preg_match("%\s*/[0-9]*/\s*%", $this->route):
                list(, $id,) = explode("/", $this->route);
                $article = new Article($id, $this->bdd);
                $article->charger();
                $this->title = $article->titre;
                $this->content = $article->afficher();
                break;

            case preg_match("%s*/[0-9]*%", $this->route):
                list(, $id) = explode("/", $this->route);
                $rubrique = new Rubrique($id, $this->bdd);
                $rubrique->charger();
                $this->title = $rubrique->nom;
                $this->content = $rubrique->afficher();
                break;

            default:
                $accueil = new Accueil($this->bdd);
                $accueil->charger();
                $this->title = "promouvoir le PHP aupr&egrave;s des professionnels";
                $this->content = $accueil->afficher();
        }
    }

    function community()
    {
        $branche = new Branche($this->bdd);
        return $branche->naviguer(5, 2);
    }

    function header()
    {
        $branche = new Branche($this->bdd);
        return $branche->naviguer(21, 2);
    }

    function content()
    {
        return $this->content;
    }

    function social()
    {
        return '<ul id="menufooter-share">
                    <li>
                        <a href="' . $this->conf->obtenir('web|path') . $this->conf->obtenir('site|prefix') . $this->conf->obtenir('site|query_prefix') . 'faq/53/comment-contacter-l-afup" class="spriteshare spriteshare-mail">Nous contacter</a>
                    </li>
                    <li>
                        <a href="http://www.facebook.com/fandelafup" class="spriteshare spriteshare-facebook">L\'AFUP sur Facebook</a>
                    </li>
                    <li>
                        <a href="https://twitter.com/afup" class="spriteshare spriteshare-twitter">L\'AFUP sur Twitter</a>
                    </li>
                </ul>
                <a href="' . $this->conf->obtenir('web|path') . $this->conf->obtenir('site|prefix') . $this->conf->obtenir('site|query_prefix') . 'faq/6" id="footer-faq">Encore des questions ? <strong>F.A.Q.</strong></a>';
    }

    function footer()
    {
        $branche = new Branche($this->bdd);
        return $branche->naviguer(38, 2, "menufooter-top");
    }
}