<?php

use Phinx\Seed\AbstractSeed;

class Inscriptions extends AbstractSeed
{
    public function run()
    {
        // Inscriptions
        $data = [
            [
                'reference' => 'REF-TEST-001',
                'type_inscription' => AFUP_FORUM_2_JOURNEES,
                'montant' => $GLOBALS['AFUP_Tarifs_Forum'][AFUP_FORUM_2_JOURNEES],
                'civilite' => 'Mme',
                'nom' => 'Michu',
                'prenom' => 'Bernadette',
                'email' => 'bernadette@yahoo.fr',
                'telephone' => '0601020304',
                'citer_societe' => '1',
                'newsletter_afup' => '1',
                'newsletter_nexen' => '0',
                'id_forum' => Event::ID_FORUM,
                'etat' => AFUP_FORUM_ETAT_REGLE,
            ],
        ];

        $table = $this->table('afup_inscription_forum');
        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;

        // Facturation
        $data = [
            [
                'reference' => 'REF-TEST-001',
                'montant' => $GLOBALS['AFUP_Tarifs_Forum'][AFUP_FORUM_2_JOURNEES],
                'date_reglement' => time(),
                'type_reglement' => AFUP_FORUM_REGLEMENT_CARTE_BANCAIRE,
                'email' => 'bernadette@yahoo.fr',
                'nom' => 'Michu',
                'prenom' => 'Bernadette',
                'adresse' => '3 rue du chemin',
                'code_postal' => '99002',
                'ville' => 'Ville',
                'id_pays' => 'FR',
                'autorisation' => 'otzbfksgve',
                'transaction' => 'taedsken',
                'etat' => AFUP_FORUM_ETAT_REGLE,
                'facturation' => 0,
                'id_forum' => Event::ID_FORUM,
                'date_facture' => time(),
            ],
        ];

        $table = $this->table('afup_facturation_forum');
        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;
    }
}
