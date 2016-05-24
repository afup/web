<?php
namespace Afup\Site\Corporate;


class Accueil
{
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
        $derniers_articles = $articles->chargerDerniersAjouts(5);

        $colonne = '<div id="main" class="mod item left content w66 m50 t100">
                    <h1>Promouvoir le PHP auprès des professionnels</h1>';

        $colonne .= '<blockquote>L\'AFUP a avant tout une vocation d\'information, et fournira les éléments clefs
		     qui permettront de choisir PHP selon les véritables besoins et contraintes d\'un projet.</blockquote>
                     <p>L\'AFUP, Association Française des Utilisateurs de PHP est une association loi 1901,
		     dont le principal but est de promouvoir le langage PHP auprès des professionnels et de participer à son développement.
             L\'AFUP a été créée pour répondre à un besoin croissant des entreprises,
		     celui d\'avoir un interlocuteur unique pour répondre à leurs questions sur PHP.
		     Par ailleurs, l\'AFUP offre un cadre de rencontre et de ressources techniques
		     pour les développeurs qui souhaitent faire avancer le langage PHP lui même.</p>';

        foreach ($derniers_articles as $article) {
            $descriptif = ($article->descriptif) ? $article->descriptif : $article->chapeau;
            $colonne .= '<a href="'.$article->route().'" class="article article-teaser">';
            $colonne .= '<time datetime="'.date('Y-m-d', $article->date).'">'.date('d|m|y', $article->date).'</time>';
            $colonne .= '<h2>'.$article->titre.'</h2>';
            $colonne .= '<p>'.strip_tags($descriptif, '<p><strong>').'</p>';
            $colonne .= '</a>';
        }

        $colonne .= '</div>';

        return $colonne;
    }
}