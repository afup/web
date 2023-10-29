<?php

use AppBundle\Association\Model\User;
use Phinx\Seed\AbstractSeed;

class Users extends AbstractSeed
{
    const ID_USER_ADMIN = 1;
    const ID_USER_EXPIRIE = 2;
    const ID_USER_PERSONNE_MORALE = 3;
    const ID_USER_PERSONNE_MORALE_NON_MANAGER = 4;
    const ID_USER_PERSONNE_PHYSIQUE = 5;

    const ID_PERSONNE_MORALE_MY_CORP = 1;

    public function run()
    {
        $data = [
            [
                'id'    => self::ID_PERSONNE_MORALE_MY_CORP,
                'civilite' => '1',
                'nom' => 'Dupont',
                'prenom' => 'Raoul',
                'email' => 'raoul.dupont@mycorp.fr',
                'raison_sociale' => 'MyCorp',
                'siret' => '4445451',
                'adresse' => '12 cours Hopper',
                'code_postal' => '69001',
                'ville' => 'LYON',
                'id_pays' => 'FR',
                'etat' => 0,
                'max_members' => 3
            ],
        ];

        $table = $this->table('afup_personnes_morales');

        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;


        $data = [
            [
                'id'    => self::ID_USER_ADMIN,
                'login' => 'admin',
                'mot_de_passe' => md5('admin'),
                'nom' => 'Admin',
                'niveau' => 2, // AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
                'prenom' => 'Admin',
                'email' => 'admin@admin.fr',
                'niveau_modules' => '00000'
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
                'niveau_modules' => '00000',
            ],
            [
                'id'    => self::ID_USER_PERSONNE_MORALE,
                'login' => 'edmonddupont',
                'mot_de_passe' => md5('edmonddupont'),
                'nom' => 'Dupont',
                'niveau' => 0, // AFUP_DROITS_NIVEAU_MEMBRE,
                'prenom' => 'Edmond',
                'email' => 'edmond.dupont@mycorp.fr',
                'roles' => '["ROLE_COMPANY_MANAGER"]',
                'id_personne_morale' => self::ID_PERSONNE_MORALE_MY_CORP,
                'niveau_modules' => '00000',
            ],
            [
                'id'    => self::ID_USER_PERSONNE_MORALE_NON_MANAGER,
                'login' => 'raoul',
                'mot_de_passe' => md5('raoul'),
                'nom' => 'Raoul',
                'niveau' => 0, // AFUP_DROITS_NIVEAU_MEMBRE,
                'prenom' => 'Jan',
                'email' => 'jean.raoul@mycorp.fr',
                'roles' => '[]',
                'id_personne_morale' => self::ID_PERSONNE_MORALE_MY_CORP,
                'niveau_modules' => '00000',
                'etat' => 1
            ],
            [
                'id'    => self::ID_USER_PERSONNE_PHYSIQUE,
                'login' => 'paul',
                'mot_de_passe' => md5('paul'),
                'nom' => 'Paul',
                'prenom' => 'Personne',
                'niveau' => 0, // AFUP_DROITS_NIVEAU_MEMBRE,
                'email' => 'paul.personne@mycorp.fr',
                'roles' => '[]',
                'etat' => User::STATUS_ACTIVE,
                'niveau_modules' => '00000',
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
            ],
            [
                'date_debut' => $dateDebutUserExpire,
                'type_personne' => 1, // AFUP_COTISATION_MORALE
                'id_personne' => self::ID_PERSONNE_MORALE_MY_CORP,
                'montant' => 150,
                'date_fin' => $now + $oneMonthInSeconds * 12,
                'numero_facture' => 'COTIS-'.date('Y').'-200',
            ],
            [
                'date_debut' => $dateDebutUserExpire,
                'type_personne' => 0, // AFUP_COTISATION_PHYSIQUE
                'id_personne' => self::ID_USER_PERSONNE_PHYSIQUE,
                'montant' => 25,
                'date_fin' => $now + $oneMonthInSeconds * 12,
                'numero_facture' => 'COTIS-'.date('Y').'-201',
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
