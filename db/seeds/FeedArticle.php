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
                'afup_planete_flux_id' => 2,
                'clef' => '0482a33e-7370-11ee-b962-0242ac120002',
                'titre' => 'Un titre',
                'url' => 'https://afup.org/url.html',
                'maj' => time(),
                'auteur' => 'Un super auteur',
                'resume' => 'Un super article',
                'contenu' => 'Le contenu du super article',
                'etat' => 1
            ],
        ];

        $table = $this->table('afup_planete_billet');
        $table->truncate();

        $table
            ->insert($data)
            ->save();
    }
}
