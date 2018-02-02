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

    function social() {
        return
            '<ul id="menufooter-share">
                <li>
                    <a href="'.$this->conf->obtenir('web|path').$this->conf->obtenir('site|prefix').$this->conf->obtenir('site|query_prefix').'faq/53/comment-contacter-l-afup" class="spriteshare spriteshare-mail">Nous contacter</a>
                </li>
                <li>
                    <a href="http://www.facebook.com/fandelafup" class="spriteshare spriteshare-facebook">L\'AFUP sur Facebook</a>
                </li>
                <li>
                    <a href="https://twitter.com/afup" class="spriteshare spriteshare-twitter">L\'AFUP sur Twitter</a>
                </li>
            </ul>
                ';
    }

    function footer()
    {
        $branche = new Branche($this->bdd);

        $footerColumns = [];
        foreach ($branche->feuillesEnfants(38) as $feuilleColonne) {
            $footerColumns[] = [
                'nom' => $branche->getNom($feuilleColonne['id']),
                'items' => $branche->feuillesEnfants($feuilleColonne['id'])
            ];
        }

        return $footerColumns;
    }

    function getRightColumn() {
        $branche = new Branche($this->bdd);
        $branche->navigation_avec_image(true);
        return $branche;

        $content = '<aside id="sidebar-article" class="mod item left w33 m50 t100">';
        $content .= '<h2>L\'afup<br>organise...</h2>' . $branche->naviguer(1, 2, "externe", "");
        //twitter widget
        $content .= '<h2>Sur Twitter...</h2><a class="twitter-timeline" href="https://twitter.com/afup" data-widget-id="582135958075752448">Tweets by @afup</a>';
        $content .= '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?"http":"https";if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
        $content .= '</aside>';
        return $content;
    }
}
