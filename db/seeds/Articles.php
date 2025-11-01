<?php

declare(strict_types=1);

use Afup\Site\Corporate\Article;
use AppBundle\Site\Model\Rubrique;
use Cocur\Slugify\Slugify;
use Faker\Factory;
use Phinx\Seed\AbstractSeed;

class Articles extends AbstractSeed
{
    public function run(): void
    {
        $chapeau = <<<EOF
<p>Comme &agrave; chaque &eacute;dition du Forum PHP, les conf&eacute;rences ont &eacute;t&eacute; capt&eacute;es par notre partenaire dFusion. Elles sont d&eacute;sormais en ligne sur notre page "vid&eacute;os" !&nbsp;</p>
EOF;

        $contenu = <<<EOF
<p>&nbsp;Fid&egrave;le &agrave; notre mission de diffusion du savoir aupr&egrave;s des d&eacute;veloppeurs PHP, nous mettons en ligne les captations vid&eacute;o des conf&eacute;rences donn&eacute;es il y a &agrave; peine trois semaines lors du Forum PHP 2018.</p>
<p>Hormis la conf&eacute;rence "Cessons les estimations" de Fr&eacute;d&eacute;ric Legu&eacute;dois, qui n'&eacute;tait pas capt&eacute;e <a href="https://www.leguedois.fr/pourquoi-les-conferences-ne-sont-pas-filmees/">&agrave; sa demande</a>, tous les talks sont disponibles sur notre page "<a href="../../talks/">vid&eacute;os</a>". Faites passer &agrave; vos voisins et coll&egrave;gues, visionnez les sujets que vous avez manqu&eacute;s, revoyez ce talk qui vous a fascin&eacute;, et surtout, surtout, imaginez le plaisir de les voir en live : <strong>venez nous voir en octobre au Forum PHP 2019 ou en mai &agrave; l'AFUP Day !&nbsp;</strong></p>
EOF;

        $data = [
            [
                'titre' => "Les vidéos des talks du Forum PHP 2018 sont disponibles",
                'chapeau' => $chapeau,
                'contenu' => $contenu,
                'raccourci' => 'les-videos-du-forum-2018-en-ligne',
                'id_site_rubrique' => Rubrique::ID_RUBRIQUE_ACTUALITES,
                'date' => 1542150000,
                'id_forum' => Event::ID_FORUM,
                'etat' => 1,
                'type_contenu' => Article::TYPE_CONTENU_HTML,
            ],
        ];

        $data[] = $this->createMarkdownArticle();
        $data[] = $this->createHTMLArticle();

        $slugger = Slugify::create();
        $faker = Factory::create();
        for ($i = 1; $i < 15; $i++) {
            $titre = $faker->text(80);
            $data[] = [
                'titre' => $titre,
                'chapeau' => $faker->text(200),
                'contenu' => '<p>' . implode('</p><p>', $faker->paragraphs(10)) . '</p>',
                'raccourci' => $slugger->slugify($titre),
                'id_site_rubrique' => Rubrique::ID_RUBRIQUE_ACTUALITES,
                'date' => $faker->unixTime(new DateTime('2017-12-31T23:59:59')),
                'etat' => 1,
                'type_contenu' => Article::TYPE_CONTENU_HTML,
            ];
        }

        $table = $this->table('afup_site_article');
        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;
    }

    private function createMarkdownArticle(): array
    {
        $contenu = <<<MARKDOWN
### Un premier titre !
Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam aperiam dolor, eligendi expedita nisi quibusdam repellendus repudiandae!

### Encore un titre
Un peu **de texte en gras**.
<br><br>
Et un peu *de texte en italic*.

### Une dernière section
Un texte avec un lien [commodi delectus](https://afup.org) et encore un peu de texte.
MARKDOWN;

        return [
            'titre' => "Un article en Markdown",
            'chapeau' => "*Un peu* de text **avec de la mise** en forme",
            'contenu' => $contenu,
            'raccourci' => 'un-article-en-markdown',
            'id_site_rubrique' => Rubrique::ID_RUBRIQUE_ACTUALITES,
            'date' => 1761859722,
            'id_forum' => Event::ID_FORUM,
            'etat' => 1,
            'type_contenu' => Article::TYPE_CONTENU_MARKDOWN,
        ];
    }

    private function createHTMLArticle(): array
    {
        $contenu = <<<HTML
<h3>Un premier titre !</h3>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam aperiam dolor, eligendi expedita nisi quibusdam repellendus repudiandae!</p>

<h3>Encore un titre</h3>
<p>Un peu <strong>de texte en gras</strong>.
<br><br>
Et un peu <em>de texte en italic</em>.</p>

<h3>Une dernière section</h3>
<p>Un texte avec un lien <a href="https://afup.org">commodi delectus</a> et encore un peu de texte.
<br><br>
<strong>Un peu de gras
avec un saut de ligne en base</strong></p>
HTML;

        return [
            'titre' => "Un article en HTML",
            'chapeau' => "<p>Lorem <strong>ipsum</strong> dolor si amet.</p>",
            'contenu' => $contenu,
            'raccourci' => 'un-article-en-html',
            'id_site_rubrique' => Rubrique::ID_RUBRIQUE_ACTUALITES,
            'date' => 1761858722,
            'id_forum' => Event::ID_FORUM,
            'etat' => 1,
            'type_contenu' => Article::TYPE_CONTENU_HTML,
        ];
    }
}
