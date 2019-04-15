<?php

use Phinx\Seed\AbstractSeed;

class ComptaOperation extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'operation' => 'Depense',
            ],
            [
                'id' => 2,
                'operation' => 'Recette',
            ],
        ];

        $table = $this->table('compta_operation');
        $table->truncate();

        $table
            ->insert($data)
            ->save();
    }
}

