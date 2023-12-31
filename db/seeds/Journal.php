<?php

use Phinx\Seed\AbstractSeed;

class Journal extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'id'    => '1',
                'date_ecriture' => '2023-11-12',
                'numero_operation' => 'NTU-342',
                'nom_frs' => '',
                'montant' => 15.8,
                'description' => 'PRLV SEPA ONLINE SAS SCW SCALEWAY',
                'comment' => null,
                'attachment_required' => 1,
                'idcompte' => 1,
            ],
            [
                'id'    => '2',
                'date_ecriture' => '2024-02-16',
                'numero_operation' => 'NTU-344',
                'nom_frs' => '',
                'montant' => 15.6,
                'description' => 'PRLV SEPA ONLINE SAS SCW SCALEWAY',
                'comment' => null,
                'attachment_required' => 1,
                'idcompte' => 1,
                'montant_ht_soumis_tva_20' => 13,
            ],
        ];

        $table = $this->table('compta');

        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;
    }
}
