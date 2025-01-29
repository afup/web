<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class ComptaCompte extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'nom_compte' => 'Compte courant',
            ],
            [
                'id' => 2,
                'nom_compte' => 'Caisse',
            ],
            [
                'id' => 3,
                'nom_compte' => 'Livret A',
            ],
            [
                'id' => 4,
                'nom_compte' => 'Paypal',
            ],
        ];

        $table = $this->table('compta_compte');
        $table->truncate();

        $table
            ->insert($data)
            ->save();
    }
}
