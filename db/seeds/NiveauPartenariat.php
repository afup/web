<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class NiveauPartenariat extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            ['titre' => 'Platine'],
            ['titre' => 'Or'],
            ['titre' => 'Argent'],
            ['titre' => 'Bronze'],
        ];

        $table = $this->table('afup_niveau_partenariat');
        $table->truncate();

        $table
            ->insert($data)
            ->save();
    }
}
