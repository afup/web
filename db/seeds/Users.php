<?php

declare(strict_types=1);

use AppBundle\Association\Model\User;
use Phinx\Seed\AbstractSeed;

class Users extends AbstractSeed
{
    const ID_USER_ADMIN = 1;
    const ID_USER_EXPIRIE = 2;
    const ID_USER_PERSONNE_MORALE = 3;
    const ID_USER_PERSONNE_MORALE_NON_MANAGER = 4;
    const ID_USER_PERSONNE_PHYSIQUE = 5;
    const ID_USER_PERSONNE_PHYSIQUE_FIXED_COTISATIONS = 6;
    const ID_USER_PERSONNE_LIE_PERSONNE_MORALE_FIXED_COTISATIONS = 7;

    const ID_PERSONNE_MORALE_MY_CORP = 1;
    const ID_PERSONNE_MORALE_HELIOS_AEROSPACE = 2;

    public function run(): void
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
                'etat' => 1,
                'public_profile_enabled' => 1,
                'max_members' => 3,
            ],
            [
                'id'    => self::ID_PERSONNE_MORALE_HELIOS_AEROSPACE,
                'civilite' => '1',
                'nom' => 'Ayesa',
                'prenom' => 'Dev',
                'email' => 'dev.ayesa@helios-aerospace.com',
                'raison_sociale' => 'Helios Aerospace',
                'siret' => '789456123',
                'adresse' => '8, main road',
                'code_postal' => '77201',
                'ville' => 'Houston, TX',
                'id_pays' => 'FR',
                'etat' => 1,
                'public_profile_enabled' => 1,
                'max_members' => 3,
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
                'niveau_modules' => '00000',
                'roles' => '',
                'adresse' => '',
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
                'roles' => '',
                'adresse' => '',
            ],
            // utilisateurs avec des cotisations à dates fixes pour les tests
            [
                'id'    => self::ID_USER_PERSONNE_PHYSIQUE_FIXED_COTISATIONS,
                'login' => 'cpike',
                'mot_de_passe' => md5('cpike'),
                'nom' => 'Pike',
                'niveau' => 0, // AFUP_DROITS_NIVEAU_MEMBRE,
                'prenom' => 'Christopher',
                'email' => 'cpike@strafleet.fr',
                'niveau_modules' => '00000',
                'adresse' => '15, main road',
                'code_postal' => '93501-1100',
                'ville' => 'Mojave, CA',
                'roles' => '',
            ],
            [
                'id'    => self::ID_USER_PERSONNE_LIE_PERSONNE_MORALE_FIXED_COTISATIONS,
                'login' => 'dayesa',
                'mot_de_passe' => md5('dayesa'),
                'nom' => 'Ayesa',
                'niveau' => 0, // AFUP_DROITS_NIVEAU_MEMBRE,
                'prenom' => 'Dev',
                'email' => 'dev.ayesa@helios-aerospace.com',
                'roles' => '["ROLE_COMPANY_MANAGER"]',
                'id_personne_morale' => self::ID_PERSONNE_MORALE_HELIOS_AEROSPACE,
                'niveau_modules' => '00000',
                'adresse' => '',
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
                'adresse' => '',
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
                'etat' => 1,
                'adresse' => '',
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
                'adresse' => '',
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
        $year = (int) date('Y');

        $data = [
            [
                'date_debut' => $now - $oneMonthInSeconds,
                'type_personne' => AFUP_PERSONNES_PHYSIQUES,
                'id_personne' => self::ID_USER_ADMIN,
                'montant' => 25,
                'date_fin' => $now + $oneMonthInSeconds * 12,
            ],
            [
                'date_debut' => $dateDebutUserExpire,
                'type_personne' => AFUP_PERSONNES_PHYSIQUES,
                'id_personne' => self::ID_USER_EXPIRIE,
                'montant' => 25,
                'date_fin' => $dateDebutUserExpire + $oneMonthInSeconds * 12,
                'numero_facture' => 'COTIS-2018-198',
            ],
            [
                'date_debut' => $dateDebutUserExpire,
                'type_personne' => AFUP_PERSONNES_MORALES,
                'id_personne' => self::ID_PERSONNE_MORALE_MY_CORP,
                'montant' => 150,
                'date_fin' => $now + $oneMonthInSeconds * 12,
                'numero_facture' => 'COTIS-' . date('Y') . '-200',
            ],
            [
                'date_debut' => $dateDebutUserExpire,
                'type_personne' => AFUP_PERSONNES_PHYSIQUES,
                'id_personne' => self::ID_USER_PERSONNE_PHYSIQUE,
                'montant' => 25,
                'date_fin' => $now + $oneMonthInSeconds * 12,
                'numero_facture' => 'COTIS-' . date('Y') . '-' . (date('Hi')+200),
            ],
            [
                'date_debut' => mktime(16, 10, 10, 1, 1, 2023),
                'type_personne' => AFUP_PERSONNES_PHYSIQUES,
                'id_personne' => self::ID_USER_PERSONNE_PHYSIQUE_FIXED_COTISATIONS,
                'montant' => 30,
                'date_fin' => mktime(16, 10, 10, 1, 1, 2024),
                'numero_facture' => 'COTIS-2023-1',
            ],
            [
                'date_debut' => mktime(16, 10, 10, 1, 1, 2024),
                'type_personne' => AFUP_PERSONNES_PHYSIQUES,
                'id_personne' => self::ID_USER_PERSONNE_PHYSIQUE_FIXED_COTISATIONS,
                'montant' => 30,
                'date_fin' => mktime(16, 10, 10, 1, 1, 2025),
                'numero_facture' => 'COTIS-2024-245',
            ],
            [
                'date_debut' => mktime(16, 10, 10, 1, 1, $year),
                'type_personne' => AFUP_PERSONNES_PHYSIQUES,
                'id_personne' => self::ID_USER_PERSONNE_PHYSIQUE_FIXED_COTISATIONS,
                'montant' => 30,
                'date_fin' => mktime(16, 10, 10, 1, 1, $year+1),
                'numero_facture' => "COTIS-$year-245",
            ],
            [
                'date_debut' => mktime(16, 10, 10, 1, 2, 2023),
                'type_personne' => AFUP_PERSONNES_MORALES,
                'id_personne' => self::ID_PERSONNE_MORALE_HELIOS_AEROSPACE,
                'montant' => 150,
                'date_fin' => mktime(16, 10, 10, 1, 2, 2024),
                'numero_facture' => 'COTIS-2023-2',
            ],
            [
                'date_debut' => mktime(16, 10, 10, 1, 2, 2024),
                'type_personne' => AFUP_PERSONNES_MORALES,
                'id_personne' => self::ID_PERSONNE_MORALE_HELIOS_AEROSPACE,
                'montant' => 180,
                'date_fin' => mktime(16, 10, 10, 1, 2, 2025),
                'numero_facture' => 'COTIS-2024-249',
            ],
            [
                'date_debut' => mktime(16, 10, 10, 1, 2, $year),
                'type_personne' => AFUP_PERSONNES_MORALES,
                'id_personne' => self::ID_PERSONNE_MORALE_HELIOS_AEROSPACE,
                'montant' => 180,
                'date_fin' => mktime(16, 10, 10, 1, 2, $year+1),
                'numero_facture' => "COTIS-$year-249",
            ],
        ];

        $table = $this->table('afup_cotisations');

        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;
    }
}
