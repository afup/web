<?php

use Phinx\Seed\AbstractSeed;

class EventTarif extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'id_tarif' => 1,
                'id_event' => Event::ID_FORUM,
                'price' => 150,
                'date_start' => '2010-01-01 10:00:00',
                'date_end' => '2099-12-31 23:59:59',
            ],
            [
                'id_tarif' => 2,
                'id_event' => Event::ID_FORUM,
                'price' => 250,
                'date_start' => '2010-01-01 10:00:00',
                'date_end' => '2099-12-31 23:59:59',
            ],
            [
                'id_tarif' => 3,
                'id_event' => Event::ID_FORUM,
                'price' => 150,
                'date_start' => '2010-01-01 10:00:00',
                'date_end' => '2099-12-31 23:59:59',
            ],
            [
                'id_tarif' => 5,
                'id_event' => Event::ID_FORUM,
                'price' => 2,
                'date_start' => '2010-01-01 10:00:00',
                'date_end' => '2099-12-31 23:59:59',
            ],
        ];

        $table = $this->table('afup_forum_tarif_event');
        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;
    }
}
