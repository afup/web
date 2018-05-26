<?php

use Phinx\Seed\AbstractSeed;

class Users extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'id'    => '1',
                'login' => 'admin',
                'mot_de_passe' => md5('admin'),
                'nom' => 'Admin',
                'prenom' => 'Admin',
                'email' => 'admin@admin.fr',
            ],
        ];

        $table = $this->table('afup_personnes_physiques');

        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;
    }
}
