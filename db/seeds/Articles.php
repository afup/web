<?php

declare(strict_types=1);

use AppBundle\Site\Model\Rubrique;
use Cocur\Slugify\Slugify;
use Faker\Factory;
use Phinx\Seed\AbstractSeed;

class Articles extends AbstractSeed
{
    public function run(): void
    {
        $chapeau = <<<EOF
Comme &agrave; chaque &eacute;dition du Forum PHP, les conf&eacute;rences ont &eacute;t&eacute; capt&eacute;es par notre partenaire dFusion. Elles sont d&eacute;sormais en ligne sur notre page "vid&eacute;os" !
EOF;

        $contenu = <<<EOF
Fid&egrave;le &agrave; notre mission de diffusion du savoir aupr&egrave;s des d&eacute;veloppeurs PHP, nous mettons en ligne les captations vid&eacute;o des conf&eacute;rences donn&eacute;es il y a &agrave; peine trois semaines lors du Forum PHP 2018.

Hormis la conf&eacute;rence "Cessons les estimations" de Fr&eacute;d&eacute;ric Legu&eacute;dois, qui n'&eacute;tait pas capt&eacute;e [&agrave; sa demande](https://www.leguedois.fr/pourquoi-les-conferences-ne-sont-pas-filmees/), tous les talks sont disponibles sur notre page "[vid&eacute;os](../../talks/)". Faites passer &agrave; vos voisins et coll&egrave;gues, visionnez les sujets que vous avez manqu&eacute;s, revoyez ce talk qui vous a fascin&eacute;, et surtout, surtout, imaginez le plaisir de les voir en live : **venez nous voir en octobre au Forum PHP 2019 ou en mai &agrave; l'AFUP Day !**
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
            ],
        ];

        $data[] = $this->createMarkdownArticle();

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
        ];
    }
}
