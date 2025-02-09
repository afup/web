<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class ComptaReglement extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'reglement'=>'Espece',
            ],
            [
                'id' => 2,
                'reglement'=>'Carte Bleue',
            ],
            [
                'id' => 3,
                'reglement'=>'Virement',
            ],
            [
                'id' => 4,
                'reglement'=>'Cheque',
            ],
            [
                'id' => 5,
                'reglement'=>'Prelevement',
            ],
            [
                'id' => 6,
                'reglement'=>'Solde banque',
            ],
            [
                'id' => 7,
                'reglement'=>'Provision',
            ],
            [
                'id' => 8,
                'reglement'=>'paypal',
            ],
            [
                'id' => 9,
                'reglement'=>'A déterminer',
            ],
        ];

        $table = $this->table('compta_reglement');
        $table->truncate();

        $table
            ->insert($data)
            ->save();
    }
}
