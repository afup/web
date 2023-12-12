<?php

use Phinx\Seed\AbstractSeed;

class Compta extends AbstractSeed
{
    public function run()
    {
        $dir = 'htdocs/uploads/202310/';
        if (!is_dir($dir)) {
            if (!mkdir($dir) && !is_dir($dir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
            }
        }

        copy('tests/behat/files/test_file1.pdf', $dir.'test_file1.pdf');

        $data = [
            [
                'idoperation' => 2,
                'idcategorie' => 34,
                'nom_frs' => 'Prestabox',
                'tva_intra' => 'FR5512345',
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
                'nom_frs' => 'Un fournisseur',
                'tva_intra' => 'FR9912345',
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
