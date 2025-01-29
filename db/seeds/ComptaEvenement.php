<?php

declare(strict_types=1);


use Phinx\Seed\AbstractSeed;

class ComptaEvenement extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'evenement' => 'Forum 2008',
            ],
            [
                'id' => 2,
                'evenement' => 'RV AFUP',
            ],
            [
                'id' => 28,
                'evenement' => 'Forum 2012',
            ],
            [
                'id' => 4,
                'evenement' => 'Forum 2007',
            ],
            [
                'id' => 5,
                'evenement' => 'AG',
            ],
            [
                'id' => 8,
                'evenement' => 'A déterminer',
            ],
            [
                'id' => 9,
                'evenement' => 'Barcamp',
            ],
            [
                'id' => 10,
                'evenement' => 'Salon Solution Linux',
            ],
            [
                'id' => 13,
                'evenement' => 'Site Internet',
            ],
            [
                'id' => 14,
                'evenement' => 'PHP TV',
            ],
            [
                'id' => 15,
                'evenement' => 'Journee Dev',
            ],
            [
                'id' => 16,
                'evenement' => 'Stock',
            ],
            [
                'id' => 17,
                'evenement' => 'Forum 2009',
            ],
            [
                'id' => 19,
                'evenement' => 'Livre blanc',
            ],
            [
                'id' => 21,
                'evenement' => 'Symfony live 2009',
            ],
            [
                'id' => 22,
                'evenement' => 'Forum 2010',
            ],
            [
                'id' => 29,
                'evenement' => 'PHP Tour Nantes 2012',
            ],
            [
                'id' => 24,
                'evenement' => 'Forum 2011',
            ],
            [
                'id' => 25,
                'evenement' => 'PHP Tour Lille 2011',
            ],
            [
                'id' => 26,
                'evenement' => 'Gestion',
            ],
            [
                'id' => 27,
                'evenement' => 'Association AFUP',
            ],
            [
                'id' => 30,
                'evenement' => 'Drupagora 2012',
            ],
            [
                'id' => 31,
                'evenement' => 'Salon Solution Linux 2012',
            ],
            [
                'id' => 32,
                'evenement' => 'Forum 2013',
            ],
            [
                'id' => 33,
                'evenement' => 'PHP Tour Lyon 2014',
            ],
            [
                'id' => 34,
                'evenement' => 'ZendCon 2013',
            ],
            [
                'id' => 35,
                'evenement' => 'Forum 2014',
            ],
            [
                'id' => 36,
                'evenement' => 'PHP Tour Luxembourg 2015',
            ],
            [
                'id' => 37,
                'evenement' => 'Forum 2015',
            ],
            [
                'id' => 38,
                'evenement' => 'PHP Tour Clermont Ferrand 2016',
            ],
            [
                'id' => 39,
                'evenement' => 'Open Source Summit 2015',
            ],
            [
                'id' => 40,
                'evenement' => 'Forum 2016',
            ],
            [
                'id' => 41,
                'evenement' => 'Forum 2017',
            ],
            [
                'id' => 42,
                'evenement' => 'PHP Tour Nantes 2017',
            ],
            [
                'id' => 43,
                'evenement' => 'Antennes AFUP',
            ],
            [
                'id' => 44,
                'evenement' => 'PHP Tour Montpellier 2018',
            ],
            [
                'id' => 45,
                'evenement' => 'Forum 2018',
            ],
            [
                'id' => 46,
                'evenement' => 'AFUP Day 2019',
            ],
            [
                'id' => 47,
                'evenement' => 'AFUP Day 2019 Lyon',
            ],
            [
                'id' => 48,
                'evenement' => 'AFUP Day 2019 Lille',
            ],
            [
                'id' => 49,
                'evenement' => 'AFUP Day 2019 Rennes',
            ],
            [
                'id' => 50,
                'evenement' => 'Forum 2019',
            ],
            [
                'id' => 52,
                'evenement' => 'Divers',
            ],
        ];

        $table = $this->table('compta_evenement');
        $table->truncate();

        $table
            ->insert($data)
            ->save();
    }
}
