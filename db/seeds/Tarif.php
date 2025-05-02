<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class Tarif extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'technical_name' => 'AFUP_FORUM_DEUXIEME_JOURNEE',
                'pretty_name' => 'Deuxième journée',
                'public' => true,
                'members_only' => 0,
                'default_price' => 150,
                'active' => true,
                'day' => 'two',
                'cfp_submitter_only' => 0,
            ],
            [
                'id' => 2,
                'technical_name' => 'AFUP_FORUM_2_JOURNEES',
                'pretty_name' => '2 Jours',
                'public' => true,
                'members_only' => 0,
                'default_price' => 250,
                'active' => true,
                'day' => 'one,two',
                'cfp_submitter_only' => 0,
            ],
            [
                'id' => 3,
                'technical_name' => 'AFUP_FORUM_2_JOURNEES_AFUP',
                'pretty_name' => '2 Jours AFUP',
                'public' => true,
                'members_only' => 1,
                'default_price' => 150,
                'active' => true,
                'day' => 'one,two',
                'cfp_submitter_only' => 0,
            ],
            [
                'id' => 4,
                'technical_name' => 'AFUP_TEST',
                'pretty_name' => 'Pour les tests',
                'public' => true,
                'members_only' => 1,
                'default_price' => 100,
                'active' => true,
                'day' => 'one,two',
                'cfp_submitter_only' => 0,
            ],
            [
                'id' => 5,
                'technical_name' => 'AFUP_CFP',
                'pretty_name' => 'Spécial CFP',
                'public' => true,
                'members_only' => 0,
                'default_price' => 2,
                'active' => true,
                'day' => 'one,two',
                'cfp_submitter_only' => 1,
            ],
            [
                'id' => 108,
                'technical_name' => 'AFUP_FORUM_SPECIAL_PRICE',
                'pretty_name' => 'Spécial Forum',
                'public' => true,
                'members_only' => 0,
                'default_price' => 2,
                'active' => true,
                'day' => 'one,two',
                'cfp_submitter_only' => 1,
            ],
        ];

        $table = $this->table('afup_forum_tarif');
        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;
    }
}
