<?php

use Phinx\Seed\AbstractSeed;

class FeedArticle extends AbstractSeed
{
    public function run()
    {
        $feed = [
            'nom' => 'Un super site PHP',
            'url' => 'https://afup.org',
            'feed' => 'https://afup.org/rss.xml',
            'etat' => 0,
        ];
        $table = $this->table('afup_planete_flux');
        $table->truncate();

        $table
            ->insert($feed)
            ->save();

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
                'etat' => 1
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
                'etat' => 1
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
                'etat' => 0
            ],
        ];

        $table = $this->table('afup_planete_billet');
        $table->truncate();

        $table
            ->insert($data)
            ->save();
    }
}
