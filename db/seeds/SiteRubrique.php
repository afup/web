<?php

use Phinx\Seed\AbstractSeed;

class SiteRubrique extends AbstractSeed
{

    const ID_ACTUALITES = 9; // cf Rubrique::ID_RUBRIQUE_ACTUALITES

    public function run()
    {
        $data = [
            [
                'id' => self::ID_ACTUALITES,
                'id_parent' => 0,
                'nom' => "Actualités",
                'etat' => 1,
            ],
        ];

        $table = $this->table('afup_site_rubrique');
        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;
    }
}
