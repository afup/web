<?php

use Phinx\Seed\AbstractSeed;

class Users extends AbstractSeed
{
    const ID_USER_ADMIN = 1;
    const ID_USER_EXPIRIE = 2;

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
            // utilisateur ayant expiré, avec une date de cotisation fixe, utile pour les tests
            [
                'id'    => self::ID_USER_EXPIRIE,
                'login' => 'userexpire',
                'mot_de_passe' => md5('userexpire'),
                'nom' => 'Maurice',
                'niveau' => 0, // AFUP_DROITS_NIVEAU_MEMBRE,
                'prenom' => 'Jean',
                'email' => 'userexpire@yahoo.fr',
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

        $dateDebutUserExpire = mktime(17, 32, 15, 7, 13,2018);

        $data = [
            [
                'date_debut' => $now - $oneMonthInSeconds,
                'type_personne' => 0, // AFUP_PERSONNE_PHYSIQUE
                'id_personne' => self::ID_USER_ADMIN,
                'montant' => 25,
                'date_fin' => $now + $oneMonthInSeconds * 12,
            ],
            [
                'date_debut' => $dateDebutUserExpire,
                'type_personne' => 0, // AFUP_PERSONNE_PHYSIQUE
                'id_personne' => self::ID_USER_EXPIRIE,
                'montant' => 25,
                'date_fin' => $dateDebutUserExpire + $oneMonthInSeconds * 12,
                'numero_facture' => 'COTIS-2018-198',
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
