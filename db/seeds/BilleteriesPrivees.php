<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class BilleteriesPrivees extends AbstractSeed
{
    public const string TOKEN = 'token-billetterie-privee-test';

    public function run(): void
    {
        $now = new DateTime('now');

        // Seed idempotent : suppression de la donnee de test si elle existe deja
        $this->execute(sprintf("DELETE FROM afup_forum_billeterie_privee WHERE token = '%s'", self::TOKEN));

        $data = [
            [
                'id_forum' => Event::ID_FORUM,
                'nom' => 'Billetterie privée de test - école XYZ',
                'token' => self::TOKEN,
                'mot_de_passe' => password_hash('password', PASSWORD_DEFAULT),
                'id_tarif' => Tarif::TYPE_2_DAYS['id'],
                'prix' => 80.0,
                'max_places' => 50,
                'date_debut' => $now->format('Y-m-d H:i:s'),
                'date_fin' => $now->modify('+1 month')->format('Y-m-d H:i:s'),
                'created_on' => $now->format('Y-m-d H:i:s'),
            ],
        ];

        $this->table('afup_forum_billeterie_privee')->insert($data)->saveData();
    }
}
