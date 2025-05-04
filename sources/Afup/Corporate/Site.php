<?php

declare(strict_types=1);
namespace Afup\Site\Corporate;

use Afup\Site\Utils\Base_De_Donnees;

class Site
{
    const WEB_PATH = '/';
    const WEB_PREFIX = 'pages/site/';
    const WEB_QUERY_PREFIX = '?route=';

    /**
     * @var Base_De_Donnees
     */
    protected $bdd;

    public function __construct($bdd = false)
    {
        $this->bdd = $bdd ?: new _Site_Base_De_Donnees();
    }

    public static function raccourcir($texte, string $separator = '-'): ?string
    {
        $texte = str_replace('�', 'e', $texte);
        $texte = iconv('ISO-8859-15', 'ASCII//TRANSLIT', trim($texte));
        $pattern = ['/[^a-z0-9]/',
            '/' . $separator . $separator . '+/',
            '/^' . $separator . '/',
            '/' . $separator . '$/'];
        $replacement = [$separator, $separator, '', ''];
        return preg_replace($pattern, $replacement, strtolower($texte));
    }

    public static function transformer_lien_spip($texte)
    {
        $texte = preg_replace('`\[(.*?)[[:space:]]*->http://(.*?)\]`', '<a href="http://$2">$1</a>', (string) $texte);
        return preg_replace('`\[(.*?)->(.*?)\]`', '<a href="http://$2">$1</a>', (string) $texte);
    }

    public static function transformer_liste_spip($texte): string
    {
        $lignes = explode("\n", (string) $texte);
        foreach ($lignes as &$ligne) {
            $ligne = preg_replace("`^- (.*)`", "<ul>\n<li>\$1</li>\n</ul>", $ligne);
        }
        $texte = implode("\n", $lignes);
        return str_replace("</ul>\n<ul>\n", '', $texte);
    }

    public static function transformer_spip_en_html($texte): string
    {
        $texte = self::transformer_lien_spip($texte);
        for ($i = 0; $i < 2; $i++) {
            $texte = preg_replace('`\{\{\{[[:space:]]*(.*?)[[:space:]]*\}\}\}`', '<h3>$1</h3>', (string) $texte);
            $texte = preg_replace('`\{\{[[:space:]]*(.*?)[[:space:]]*\}\}`', '<strong>$1</strong>', (string) $texte);
            $texte = preg_replace('`\{[[:space:]]*(.*?)[[:space:]]*\}`', '<em>$1</em>', (string) $texte);
        }
        return self::transformer_liste_spip($texte);
    }

    public function importer_spip(): void
    {
        $this->bdd->executer('TRUNCATE TABLE afup_site_article');
        $this->bdd->executer('TRUNCATE TABLE afup_site_rubrique');

        $requete = 'SELECT * FROM spip_rubriques';
        $rubriques_spip = $this->bdd->obtenirTous($requete);

        $nombre_rubriques = 0;
        foreach ($rubriques_spip as $rubrique_spip) {
            if ($rubrique_spip['statut'] != "prive") {
                $rubrique = new Rubrique($rubrique_spip['id_rubrique']);
                $rubrique->id_parent = $rubrique_spip['id_parent'];
                $rubrique->position = 0;
                $rubrique->date = time();
                $rubrique->nom = ($rubrique_spip['titre']);
                $rubrique->raccourci = self::raccourcir($rubrique_spip['titre']);
                $rubrique->descriptif = self::transformer_spip_en_html(($rubrique_spip['descriptif']));
                $rubrique->contenu = self::transformer_spip_en_html(($rubrique_spip['texte']));
                $rubrique->etat = 1;
                $rubrique->inserer();
                $nombre_rubriques++;
            }
        }

        $requete = 'SELECT * FROM spip_articles';
        $articles_spip = $this->bdd->obtenirTous($requete);
        $nombre_articles = 0;
        foreach ($articles_spip as $article_spip) {
            if ($article_spip['statut'] == "publie") {
                $article = new Article($article_spip['id_article'], $this->bdd);
                $article->id_site_rubrique = $article_spip['id_rubrique'];
                $article->titre = ($article_spip['titre']);
                $article->raccourci = self::raccourcir($article_spip['titre']);
                $article->chapeau = self::transformer_spip_en_html(($article_spip['chapo']));
                $article->contenu = self::transformer_spip_en_html(($article_spip['texte']));
                $article->position = 0;
                $article->date = strtotime((string) $article_spip['date']);
                $article->etat = 1;
                $article->inserer();
                $nombre_articles++;
            }
        }
    }
}
