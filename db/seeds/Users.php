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
                'niveau' => 2, // AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
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

        $now = time();
        $oneMonthInSeconds = 60*60*24*30;

        $data = [
            [
                'date_debut' => $now - $oneMonthInSeconds,
                'type_personne' => 0, // AFUP_PERSONNE_PHYSIQUE
                'id_personne' => self::ID_USER_ADMIN,
                'montant' => 25,
                'date_fin' => $now + $oneMonthInSeconds * 12,
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
