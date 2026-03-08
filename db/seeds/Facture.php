<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class Facture extends AbstractSeed
{
    public function run(): void
    {
        $lastYear = date('Y') - 1;
        $lastYear2 = date('Y') - 2;

        $data = [
            [
                'id'    => '1',
                'date_devis' => "$lastYear2-06-10",
                'numero_devis' => "$lastYear2-01",
                'date_facture' => "$lastYear2-06-11",
                'numero_facture' => "$lastYear2-01",
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
                'ref_clt1' => "Forum PHP $lastYear2",
                'service' => '',
                'observation' => '',
                'ref_clt2' => '',
                'ref_clt3' => '',
                'tel' => '',
            ],
            [
                'id'    => '2',
                'date_devis' => "$lastYear-01-03",
                'numero_devis' => "$lastYear-02",
                'date_facture' => "$lastYear-01-04",
                'numero_facture' => "$lastYear-02",
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
                'ref_clt1' => "Forum PHP $lastYear",
                'service' => '',
                'observation' => '',
                'ref_clt2' => '',
                'ref_clt3' => '',
                'tel' => '',

            ],
            [
                'id'    => '3',
                'date_devis' => "$lastYear-01-02",
                'numero_devis' => "$lastYear-01",
                'date_facture' => null,
                'numero_facture' => null,
                'societe' => 'A company Ltd',
                'adresse' => '3, rue du port',
                'code_postal' => '29000',
                'ville' => 'Quimper',
                'id_pays' =>  'FR',
                'email' => 'mail@testmail.fr',
                'tva_intra' => null,
                'nom' => 'Le kellen',
                'prenom' => 'Yan',
                'etat_paiement' => 0,
                'devise_facture' => 'EUR',
                'ref_clt1' => "Forum PHP $lastYear",
                'service' => '',
                'observation' => '',
                'ref_clt2' => '',
                'ref_clt3' => '',
                'tel' => '',
            ],
            [
                'id'    => '4',
                'date_devis' => "$lastYear-03-02",
                'numero_devis' => "$lastYear-03",
                'date_facture' => null,
                'numero_facture' => null,
                'societe' => 'My company Ltd',
                'adresse' => '3, rue du port',
                'code_postal' => '29000',
                'ville' => 'Quimper',
                'id_pays' =>  'FR',
                'email' => 'mail@testmail.fr',
                'tva_intra' => null,
                'nom' => 'Le kellen',
                'prenom' => 'Yan',
                'etat_paiement' => 0,
                'devise_facture' => null,
                'ref_clt1' => "Forum PHP $lastYear",
                'service' => '',
                'observation' => '',
                'ref_clt2' => '',
                'ref_clt3' => '',
                'tel' => '',
            ],
            // on a besoin d'une facture avant 2024 pour tester les exports sans TVA
            [
                'id'    => '5',
                'date_devis' => "2023-06-10",
                'numero_devis' => "2023-01",
                'date_facture' => "2023-06-11",
                'numero_facture' => "2023-01",
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
                'ref_clt1' => "Forum PHP 2023",
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
                'ref' => "forum_php_$lastYear2",
                'designation' => "Forum PHP $lastYear2 - Sponsoring Bronze",
                'quantite' => '1',
                'pu' =>  '1000',
                'tva' => '0.00',
            ],
            [
                'idafup_compta_facture' => '2',
                'ref' => "forum_php_$lastYear",
                'designation' => "Forum PHP $lastYear - Sponsoring Bronze",
                'quantite' => '1',
                'pu' =>  '1000',
                'tva' => '20',
            ],
            [
                'idafup_compta_facture' => '3',
                'ref' => "forum_php_$lastYear",
                'designation' => "Forum PHP $lastYear - Sponsoring Bronze",
                'quantite' => '1',
                'pu' =>  '1000',
                'tva' => '20',
            ],
            [
                'idafup_compta_facture' => '5',
                'ref' => "forum_php_2023",
                'designation' => "Forum PHP 2023 - Sponsoring Bronze",
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
