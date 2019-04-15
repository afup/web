<?php

use Phinx\Seed\AbstractSeed;

class ComptaPeriode extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'id'=>1,
                'date_debut'=>'2007-01-01',
                'date_fin'=>'2007-12-31',
                'verouiller'=>0,
            ],
            [
                'id'=>2,
                'date_debut'=>'2008-01-01',
                'date_fin'=>'2008-12-31',
                'verouiller'=>0,
            ],
            [
                'id'=>3,
                'date_debut'=>'2009-01-01',
                'date_fin'=>'2009-12-31',
                'verouiller'=>0,
            ],
            [
                'id'=>4,
                'date_debut'=>'2010-01-01',
                'date_fin'=>'2010-12-31',
                'verouiller'=>0,
            ],
            [
                'id'=>5,
                'date_debut'=>'2011-01-01',
                'date_fin'=>'2011-12-31',
                'verouiller'=>0,
            ],
            [
                'id'=>6,
                'date_debut'=>'2012-01-01',
                'date_fin'=>'2012-12-31',
                'verouiller'=>0,
            ],
            [
                'id'=>7,
                'date_debut'=>'2013-01-01',
                'date_fin'=>'2013-12-31',
                'verouiller'=>0,
            ],
            [
                'id'=>8,
                'date_debut'=>'2014-01-01',
                'date_fin'=>'2014-12-31',
                'verouiller'=>0,
            ],
            [
                'id'=>9,
                'date_debut'=>'2015-01-01',
                'date_fin'=>'2015-12-31',
                'verouiller'=>0,
            ],
            [
                'id'=>10,
                'date_debut'=>'2016-01-01',
                'date_fin'=>'2016-12-31',
                'verouiller'=>0,
            ],
            [
                'id'=>11,
                'date_debut'=>'2017-01-01',
                'date_fin'=>'2017-12-31',
                'verouiller'=>0,
            ],
            [
                'id'=>12,
                'date_debut'=>'2018-01-01',
                'date_fin'=>'2018-12-31',
                'verouiller'=>0,
            ],
            [
                'id'=>13,
                'date_debut'=>'2019-01-01',
                'date_fin'=>'2019-12-31',
                'verouiller'=>0,
            ],
        ];

        $table = $this->table('compta_periode');
        $table->truncate();

        $table
            ->insert($data)
            ->save();
    }
}
