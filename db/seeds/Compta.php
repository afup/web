<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class Compta extends AbstractSeed
{
    public function run(): void
    {
        $path = date('Y10') . '/';
        $dir = 'htdocs/uploads/' . $path;
        if (!is_dir($dir) && (!mkdir($dir) && !is_dir($dir))) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        copy('tests/behat/files/test_file1.pdf', $dir . 'test_file1.pdf');

        $data = [
            [
                'id'    => '1',
                'idoperation' => 2,
                'idcategorie' => 34,
                'nom_frs' => 'Prestabox',
                'tva_intra' => 'FR5512345',
                'montant' => 1000,
                'idmode_regl' => 2,
                'date_regl' => date('Y-10-16'),
                'date_ecriture' => date('Y-10-17'),
                'description' => 'Une recette qui rapporte',
                'attachment_filename' => $path . '/test_file1.pdf',
                'idevenement' => 5,
                'idclef' => '',
                'idcompte' => 1,
                'numero' => '',
                'obs_regl' => '',
            ],
            [
                'id'    => '2',
                'idoperation' => 1,
                'idcategorie' => 34,
                'nom_frs' => 'Un fournisseur',
                'tva_intra' => 'FR9912345',
                'montant' => 500,
                'idmode_regl' => 2,
                'date_regl' => date('Y-10-17'),
                'date_ecriture' => date('Y-10-18'),
                'description' => 'Une dépense très utile',
                'idevenement' => 5,

                'idcompte' => 1,
                'idclef' => 2,
                'numero' => '',
                'obs_regl' => '',
            ],
            [
                'id'    => '3',
                'date_ecriture' => '2023-11-12',
                'numero_operation' => 'NTU-342',
                'nom_frs' => '',
                'montant' => 15.8,
                'description' => 'PRLV SEPA ONLINE SAS SCW SCALEWAY',
                'comment' => null,
                'attachment_required' => 1,
                'idcompte' => 1,
                'idclef' => 3,
                'numero' => '',
                'obs_regl' => '',
                'idoperation' => 0,
                'idcategorie' => 0,
                'idmode_regl' => 0,
                'date_regl' => null,
                'idevenement' => 0,
            ],
            [
                'id'    => '4',
                'date_ecriture' => '2024-02-16',
                'numero_operation' => 'NTU-344',
                'nom_frs' => '',
                'montant' => 15.6,
                'description' => 'PRLV SEPA ONLINE SAS SCW SCALEWAY',
                'comment' => null,
                'attachment_required' => 1,
                'idcompte' => 1,
                'montant_ht_soumis_tva_20' => 13,
                'idclef' => 4,
                'numero' => '',
                'obs_regl' => '',
                'idoperation' => 0,
                'idcategorie' => 0,
                'idmode_regl' => 0,
                'date_regl' => null,
                'idevenement' => 0,
            ],
            [
                'id'    => '6',
                'date_ecriture' => '2024-03-10',
                'numero_operation' => 'BILL-XXX',
                'nom_frs' => '',
                'montant' => 42.5,
                'description' => 'une facture',
                'comment' => null,
                'attachment_required' => 1,
                'attachment_filename' => 'facture.pdf',
                'idcompte' => 1,
                'montant_ht_soumis_tva_20' => 34,
                'idclef' => 2,
                'numero' => '',
                'obs_regl' => '',
                'idoperation' => 0,
                'idcategorie' => 0,
                'idmode_regl' => 0,
                'date_regl' => null,
                'idevenement' => 0,
            ],
            [
                'id'    => '5',
                'idoperation' => 1,
                'idcategorie' => 34,
                'nom_frs' => 'Un autre fournisseur',
                'tva_intra' => 'FR9912345',
                'montant' => 100,
                'idmode_regl' => 2,
                'date_regl' => date('Y-11-03'),
                'date_ecriture' => date('Y-11-04'),
                'description' => 'Une dépense moins utile',
                'idevenement' => 5,
                'idcompte' => 1,
                'idclef' => 2,
                'numero' => '',
                'obs_regl' => '',
            ],
        ];

        $table = $this->table('compta');
        $table->truncate();

        $table
            ->insert($data)
            ->save();
    }
}
