<?php

use Phinx\Seed\AbstractSeed;

class Users extends AbstractSeed
{
    const ID_USER_ADMIN = 1;

    public function run()
    {
        $data = [
            [
                'id'    => self::ID_USER_ADMIN,
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


        $data = [
            [
                'date_debut' => 1514761200, // 2018-01-01
                'type_personne' => 0, // AFUP_PERSONNE_PHYSIQUE
                'id_personne' => self::ID_USER_ADMIN,
                'montant' => 25,
                'date_fin' => 1546300799, // 2018-12-31
            ]
        ];

        $table = $this->table('afup_cotisations');

        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;
    }
}
