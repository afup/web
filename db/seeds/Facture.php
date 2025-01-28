<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class Facture extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'id'    => '1',
                'date_devis' => '2023-06-10',
                'numero_devis' => '2023-01',
                'date_facture' => '2023-06-11',
                'numero_facture' => '2023-01',
                'societe' => 'Krampouz',
                'adresse' => '3, rue du port',
                'code_postal' => '29000',
                'ville' => 'Quimper',
                'id_pays' =>  'FR',
                'email' => 'mail@testmail.fr',
                'tva_intra' => null,
                'nom' => 'Le kellen',
                'prenom' => 'Yan',
                'etat_paiement' => 1,
                'devise_facture' => 'EUR',
                'ref_clt1' => 'Forum PHP 2023',
                'service' => '',
                'observation' => '',
                'ref_clt2' => '',
                'ref_clt3' => '',
                'tel' => '',
            ],
            [
                'id'    => '2',
                'date_devis' => '2024-01-03',
                'numero_devis' => '2024-02',
                'date_facture' => '2024-01-04',
                'numero_facture' => '2024-02',
                'societe' => 'Krampouz',
                'adresse' => '3, rue du port',
                'code_postal' => '29000',
                'ville' => 'Quimper',
                'id_pays' =>  'FR',
                'email' => 'mail@testmail.fr',
                'tva_intra' => null,
                'nom' => 'Le kellen',
                'prenom' => 'Yan',
                'etat_paiement' => 1,
                'devise_facture' => 'EUR',
                'ref_clt1' => 'Forum PHP 2024',
                'service' => '',
                'observation' => '',
                'ref_clt2' => '',
                'ref_clt3' => '',
                'tel' => '',

            ],
        ];

        $table = $this->table('afup_compta_facture');

        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;

        $dataDetails = [
            [
                'idafup_compta_facture' => '1',
                'ref' => 'forum_php_2023',
                'designation' => 'Forum PHP 2023 - Sponsoring Bronze',
                'quantite' => '1',
                'pu' =>  '1000',
                'tva' => '0.00',
            ],
            [
                'idafup_compta_facture' => '2',
                'ref' => 'forum_php_2024',
                'designation' => 'Forum PHP 2024 - Sponsoring Bronze',
                'quantite' => '1',
                'pu' =>  '1000',
                'tva' => '20',
            ],
        ];

        $table = $this->table('afup_compta_facture_details');

        $table->truncate();

        $table
            ->insert($dataDetails)
            ->save()
        ;
    }
}
