<?php

use Phinx\Seed\AbstractSeed;

class Compta extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'idoperation' => 2,
                'idcategorie' => 34,
                'montant' => 1000,
                'idmode_regl' => 2,
                'date_regl' => '2023-10-16',
                'date_ecriture' => '2023-10-17',
                'description' => 'Une recette qui rapporte',
                'idevenement' => 5,
                'idcompte' => 1
            ],
            [
                'idoperation' => 1,
                'idcategorie' => 34,
                'montant' => 500,
                'idmode_regl' => 2,
                'date_regl' => '2023-10-17',
                'date_ecriture' => '2023-10-18',
                'description' => 'Une dépense très utile',
                'idevenement' => 5,
                'idcompte' => 1
            ],
        ];

        $table = $this->table('compta');
        $table->truncate();

        $table
            ->insert($data)
            ->save();
    }
}
