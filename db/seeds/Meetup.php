<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class Meetup extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'date' => '2025-11-08 19:00:00',
                'title' => 'Le super meetup',
                'location' => '123 rue de la fleur',
                'description' => 'Lorem ipsum dolor si amet',
                'antenne_name' => 'lyon',
            ],
        ];

        $table = $this->table('afup_meetup');
        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;
    }
}
