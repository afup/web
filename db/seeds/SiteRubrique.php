<?php

declare(strict_types=1);

use AppBundle\Site\Model\Rubrique;
use Phinx\Seed\AbstractSeed;

class SiteRubrique extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'id' => Rubrique::ID_RUBRIQUE_ACTUALITES,
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
