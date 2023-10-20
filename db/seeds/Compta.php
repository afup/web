<?php

use Phinx\Seed\AbstractSeed;

class Compta extends AbstractSeed
{
    public function run()
    {
        if (!is_dir('htdocs/uploads/202310/')) {
            mkdir('htdocs/uploads/202310/');
        }

        copy('tests/behat/files/test_file1.pdf', 'htdocs/uploads/202310/test_file1.pdf');

        $data = [
            [
                'idoperation' => 2,
                'idcategorie' => 34,
                'montant' => 1000,
                'idmode_regl' => 2,
                'date_regl' => '2023-10-16',
                'date_ecriture' => '2023-10-17',
                'description' => 'Une recette qui rapporte',
                'attachment_filename' => '202310/test_file1.pdf',
                'idevenement' => 5,
                'idcompte' => 1
            ],
            [
                'idoperation' => 1,
                'idcategorie' => 34,
                'montant' => 500,
                'idmode_regl' => 2,
                'date_regl' => '2023-10-17',
                'date_ecriture' => '2023-10-18',
                'description' => 'Une dépense très utile',
                'idevenement' => 5,
                'idcompte' => 1
            ],
        ];

        $table = $this->table('compta');
        $table->truncate();

        $table
            ->insert($data)
            ->save();
    }
}
