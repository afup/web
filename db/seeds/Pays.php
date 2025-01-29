<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class Pays extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'id'    => 'DE',
                'nom' => 'Allemagne',
            ],
            [
                'id'    => 'BE',
                'nom' => 'Belgique',
            ],
            [
                'id'    => 'JP',
                'nom' => 'Japon',
            ],
            [
                'id'    => 'SG',
                'nom' => 'Singapour',
            ],
            [
                'id'    => 'PL',
                'nom' => 'Pologne',
            ],
            [
                'id'    => 'FR',
                'nom' => 'France',
            ],
        ];

        $table = $this->table('afup_pays');

        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;
    }
}
