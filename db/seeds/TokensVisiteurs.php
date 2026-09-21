<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class TokensVisiteurs extends AbstractSeed
{
    public const string TOKEN_100 = 'token-visiteur-test-100';

    public const string TOKEN_120 = 'token-visiteur-test-120';

    public function run(): void
    {
        $now = new DateTime('now');

        // Seed idempotent : suppression de la donnee de test si elle existe deja
        $this->execute(sprintf("DELETE FROM afup_forum_special_price WHERE token IN ('%s', '%s')", self::TOKEN_100, self::TOKEN_120));

        $data = [
            [
                'id_event' => Event::ID_FORUM,
                'token' => self::TOKEN_100,
                'price' => 100.0,
                'date_start' => $now->format('Y-m-d H:i:s'),
                'date_end' => $now->modify('+1 month')->format('Y-m-d H:i:s'),
                'description' => 'Token visiteurs de test (prix : 100 €)',
                'created_on' => $now->format('Y-m-d H:i:s'),
                'creator_id' => 2,
            ],
            [
                'id_event' => Event::ID_FORUM,
                'token' => self::TOKEN_120,
                'price' => 120.0,
                'date_start' => $now->format('Y-m-d H:i:s'),
                'date_end' => $now->modify('+1 month')->format('Y-m-d H:i:s'),
                'description' => 'Token visiteurs de test (prix : 120 €)',
                'created_on' => $now->format('Y-m-d H:i:s'),
                'creator_id' => 2,
            ],
        ];

        $this->table('afup_forum_special_price')->insert($data)->saveData();
    }
}
