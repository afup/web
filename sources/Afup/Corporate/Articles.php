<?php

declare(strict_types=1);
namespace Afup\Site\Corporate;

class Articles
{
    /**
     * @var _Site_Base_De_Donnees
     */
    private $bdd;

    public function __construct($bdd = false)
    {
        $this->bdd = $bdd ?: new _Site_Base_De_Donnees();
    }

    public function obtenirListe(string $champs = '*',
                          string $ordre = 'titre',
                          $filtre = false,
                          $associatif = false)
    {
        $requete = 'SELECT';
        $requete .= '  afup_site_article.' . $champs . ', afup_site_rubrique.nom as nom_rubrique ';
        $requete .= 'FROM';
        $requete .= '  afup_site_article ';
        $requete .= 'INNER JOIN';
        $requete .= '  afup_site_rubrique on afup_site_article.id_site_rubrique = afup_site_rubrique.id ';
        $requete .= 'WHERE 1 = 1 ';
        if ($filtre) {
            $escapedFiltre = $this->bdd->echapper('%' . $filtre . '%');
            $requete .= sprintf(' AND (afup_site_article.titre LIKE %s OR afup_site_article.contenu LIKE %s) ', $escapedFiltre, $escapedFiltre);
        }
        $requete .= ' ORDER BY ' . $ordre;

        if ($associatif) {
            return $this->bdd->obtenirAssociatif($requete);
        } else {
            return $this->bdd->obtenirTous($requete);
        }
    }

    /**
     * @return Article[]
     */
    public function chargerArticlesDeRubrique($id_site_rubrique, $rowcount = null): array
    {
        $requete = ' SELECT';
        $requete .= '  * ';
        $requete .= ' FROM';
        $requete .= '  afup_site_article ';
        $requete .= ' WHERE ';
        $requete .= '  id_site_rubrique = ' . (int) $id_site_rubrique;
        $requete .= '  AND etat = 1';
        $requete .= ' ORDER BY date DESC';
        if (is_int($rowcount)) {
            $requete .= ' LIMIT 0, ' . $rowcount;
        }
        $elements = $this->bdd->obtenirTous($requete);

        $articles = [];
        if (is_array($elements)) {
            foreach ($elements as $element) {
                $article = new Article(null, $this->bdd);
                $article->remplir($element);
                $articles[] = $article;
            }
        }

        return $articles;
    }

    /**
     * @return Article[]
     */
    public function chargerDerniersAjouts($rowcount = 10): array
    {
        $requete = ' SELECT';
        $requete .= '  afup_site_article.* ';
        $requete .= ' FROM';
        $requete .= '  afup_site_article ';
        $requete .= ' INNER JOIN';
        $requete .= '  afup_site_rubrique on afup_site_article.id_site_rubrique = afup_site_rubrique.id';
        $requete .= ' WHERE afup_site_article.etat = 1 ';
        $requete .= ' AND afup_site_article.date <= UNIX_TIMESTAMP(NOW()) ';
        $requete .= ' AND id_parent <> 52 '; // On affiche pas les articles des forums
        $requete .= ' AND afup_site_rubrique.id <> ' . Rubrique::ID_RUBRIQUE_ASSOCIATION . ' '; // On affiche pas les articles de la page assocition
        $requete .= ' AND afup_site_rubrique.id <> ' . Rubrique::ID_RUBRIQUE_ANTENNES . ' '; // On affiche pas les articles de la page antennes
        $requete .= ' AND afup_site_rubrique.id <> ' . Rubrique::ID_RUBRIQUE_NOS_ACTIONS . ' '; // On affiche pas les articles de la page actions
        $requete .= ' ORDER BY date DESC';
        $requete .= ' LIMIT 0, ' . (int) $rowcount;

        $ajouts = [];
        $elements = $this->bdd->obtenirTous($requete);

        if (false === $elements) {
            return $ajouts;
        }

        foreach ($elements as $element) {
            $article = new Article(null, $this->bdd);
            $article->remplir($element);
            $ajouts[] = $article;
        }

        return $ajouts;
    }

    /**
     * @return Article[]
     */
    public function chargerDernieresQuestions(): array
    {
        $requete = ' SELECT';
        $requete .= '  * ';
        $requete .= ' FROM';
        $requete .= '  afup_site_article ';
        $requete .= ' WHERE id_site_rubrique = 6 ';
        $requete .= ' ORDER BY date DESC';
        $requete .= ' LIMIT 0, 10';

        $questions = [];
        $elements = $this->bdd->obtenirTous($requete);
        foreach ($elements as $element) {
            $article = new Article(null, $this->bdd);
            $article->remplir($element);
            $questions[] = $article;
        }

        return $questions;
    }
}
