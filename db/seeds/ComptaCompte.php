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
                'archived_at' => (new DateTime('last year'))->format('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'nom_compte' => 'Caisse',
            ],
            [
                'id' => 3,
                'nom_compte' => 'Livret A',
                'archived_at' => (new DateTime('last year'))->format('Y-m-d H:i:s'),
            ],
            [
                'id' => 4,
                'nom_compte' => 'Paypal',
            ],
            [
                'id' => 5,
                'nom_compte' => 'Courant CM',
            ],
            [
                'id' => 6,
                'nom_compte' => 'Livret A CM',
            ],
        ];

        $table = $this->table('compta_compte');
        $table->truncate();

        $table
            ->insert($data)
            ->save();
    }
}
