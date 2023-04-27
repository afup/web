<?php
namespace Afup\Site\Corporate;


class Accueil
{
    /**
     * @var _Site_Base_De_Donnees|mixed
     */
    private $bdd;

    function __construct($bdd = false)
    {
        if ($bdd) {
            $this->bdd = $bdd;
        } else {
            $this->bdd = new _Site_Base_De_Donnees();
        }
    }

    function charger()
    {

    }

    function afficher() {
        return $this->colonne_de_gauche();
    }

    function colonne_de_gauche() {
        $articles = new Articles($this->bdd);
        $derniers_articles = $articles->chargerDerniersAjouts(10);

        $colonne = '<blockquote>Apéros, rendez-vous et cycle de conférences, l\'AFUP est au coeur de la communauté PHP depuis 2000.
                    L\'AFUP vise à favoriser l’échange d’expertises et la diffusion des connaissances auprès de la communauté</blockquote>';

        foreach ($derniers_articles as $article) {
            $chapeau = $article->chapeau;
            $colonne .= '<a href="'.$article->route().'" class="article article-teaser">';
            $colonne .= '<time datetime="'.date('Y-m-d', $article->date).'">'.date('d|m|y', $article->date).'</time>';
            $colonne .= '<h2>'.$article->titre.'</h2>';
            $colonne .= '<p>'.strip_tags($chapeau, '<p><strong>').'</p>';
            $colonne .= '</a>';
        }


        return $colonne;
    }
}
