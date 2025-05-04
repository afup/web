<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class FeedArticle extends AbstractSeed
{
    public function run(): void
    {
        $table = $this->table('afup_planete_flux');
        $table->truncate();

        $feeds = [
            [
                'nom' => 'Un super site PHP',
                'url' => 'https://afup.org',
                'feed' => 'https://afup.org/rss.xml',
                'etat' => 1,
            ],
            [
                'nom' => 'Exemple avec un / à la fin',
                'url' => 'https://example.com/',
                'feed' => 'https://example.com/rss.xml',
                'etat' => 1,
            ],
        ];

        foreach ($feeds as $feed) {
            $table
                ->insert($feed)
                ->save();
        }

        $data = [
            [
                'afup_planete_flux_id' => 1,
                'clef' => '0482a33e-7370-11ee-b962-0242ac120002',
                'titre' => 'Un titre',
                'url' => 'https://afup.org/url.html',
                'maj' => time(),
                'auteur' => 'Un super auteur',
                'resume' => 'Un super article',
                'contenu' => 'Le contenu du super article',
                'etat' => 1,
            ],
            [
                'afup_planete_flux_id' => 1,
                'clef' => '460d0a22-78bd-11ee-b962-0242ac120002',
                'titre' => 'Un 2e titre',
                'url' => 'https://afup.org/url-2.html',
                'maj' => time(),
                'auteur' => 'Toujours un super auteur',
                'resume' => 'Un article qui déchire',
                'contenu' => 'Le contenu de l\'article qui déchire',
                'etat' => 1,
            ],
            [
                'afup_planete_flux_id' => 1,
                'clef' => '4d5cf2e2-78bd-11ee-b962-0242ac120002',
                'titre' => 'Un titre désactivé',
                'url' => 'https://afup.org/url-out.html',
                'maj' => time(),
                'auteur' => 'Un super désactivé',
                'resume' => 'Un super désactivé',
                'contenu' => 'Le contenu du super désactivé',
                'etat' => 0,
            ],
        ];

        for ($i = 0; $i < 20; $i++) {
            $data[] = [
                'afup_planete_flux_id' => 1,
                'clef' => '4d5cf2e2-78bd-11ee-b962-0242ac13000' . $i,
                'titre' => 'Un titre ' . $i,
                'url' => 'https://afup.org/url-' . $i . '.html',
                'maj' => time(),
                'auteur' => 'Un super auteur ' . $i,
                'resume' => 'Un super article ' . $i,
                'contenu' => 'Le contenu du super article ' . $i,
                'etat' => 1,
            ];
        }

        $data[] = [
            'afup_planete_flux_id' => 1,
            'clef' => '4d5cf2e2-78bd-11ee-b962-0242ac140000',
            'titre' => 'Un article sans host et avec / au début de son url',
            'url' => '/url-flux-1-avec-slash.html',
            'maj' => time(),
            'auteur' => 'Un super auteur',
            'resume' => 'Un super article',
            'contenu' => 'Le contenu du super article',
            'etat' => 1,
        ];

        $data[] = [
            'afup_planete_flux_id' => 1,
            'clef' => '4d5cf2e2-78bd-11ee-b962-0242ac140001',
            'titre' => 'Un article sans host et sans / au début de son url',
            'url' => 'url-flux-1-sans-slash.html',
            'maj' => time(),
            'auteur' => 'Un super auteur',
            'resume' => 'Un super article',
            'contenu' => 'Le contenu du super article',
            'etat' => 1,
        ];

        $data[] = [
            'afup_planete_flux_id' => 2,
            'clef' => '4d5cf2e2-78bd-11ee-b962-0242ac140002',
            'titre' => 'Un article sans host et avec / au début de son url',
            'url' => '/url-flux-2-avec-slash.html',
            'maj' => time(),
            'auteur' => 'Un super auteur',
            'resume' => 'Un super article',
            'contenu' => 'Le contenu du super article',
            'etat' => 1,
        ];

        $data[] = [
            'afup_planete_flux_id' => 2,
            'clef' => '4d5cf2e2-78bd-11ee-b962-0242ac140003',
            'titre' => 'Un article sans host et sans / au début de son url',
            'url' => '/url-flux-2-sans-slash.html',
            'maj' => time(),
            'auteur' => 'Un super auteur',
            'resume' => 'Un super article',
            'contenu' => 'Le contenu du super article',
            'etat' => 1,
        ];

        $table = $this->table('afup_planete_billet');
        $table->truncate();

        $table
            ->insert($data)
            ->save();
    }
}
