<?php


use Phinx\Seed\AbstractSeed;

class ComptaCategorie extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'id'=>2,
                'idevenement'=>0,
                'categorie'=>'Remboursement',
            ],
            [
                'id'=>3,
                'idevenement'=>1,
                'categorie'=>'Inscription',
            ],
            [
                'id'=>4,
                'idevenement'=>0,
                'categorie'=>'Cotisation AFUP',
            ],
            [
                'id'=>5,
                'idevenement'=>12,
                'categorie'=>'Banque - Compte courant',
            ],
            [
                'id'=>30,
                'idevenement'=>0,
                'categorie'=>'Salaire',
            ],
            [
                'id'=>28,
                'idevenement'=>0,
                'categorie'=>'Frais de compte',
            ],
            [
                'id'=>8,
                'idevenement'=>0,
                'categorie'=>'La Poste',
            ],
            [
                'id'=>10,
                'idevenement'=>12,
                'categorie'=>'Banque - Livret A',
            ],
            [
                'id'=>11,
                'idevenement'=>1,
                'categorie'=>'Communication',
            ],
            [
                'id'=>12,
                'idevenement'=>1,
                'categorie'=>'Divers',
            ],
            [
                'id'=>13,
                'idevenement'=>1,
                'categorie'=>'Goodies',
            ],
            [
                'id'=>14,
                'idevenement'=>1,
                'categorie'=>'Hotel',
            ],
            [
                'id'=>15,
                'idevenement'=>1,
                'categorie'=>'Location',
            ],
            [
                'id'=>16,
                'idevenement'=>1,
                'categorie'=>'Nourriture',
            ],
            [
                'id'=>17,
                'idevenement'=>1,
                'categorie'=>'Sponsor',
            ],
            [
                'id'=>18,
                'idevenement'=>1,
                'categorie'=>'Transport',
            ],
            [
                'id'=>20,
                'idevenement'=>0,
                'categorie'=>'Stock',
            ],
            [
                'id'=>22,
                'idevenement'=>0,
                'categorie'=>'Administratif',
            ],
            [
                'id'=>23,
                'idevenement'=>0,
                'categorie'=>'Banque - Espece',
            ],
            [
                'id'=>24,
                'idevenement'=>0,
                'categorie'=>'Banque - Paypal',
            ],
            [
                'id'=>25,
                'idevenement'=>0,
                'categorie'=>'Prestation',
            ],
            [
                'id'=>26,
                'idevenement'=>8,
                'categorie'=>'A déterminer',
            ],
            [
                'id'=>27,
                'idevenement'=>0,
                'categorie'=>'Comptabilité',
            ],
            [
                'id'=>31,
                'idevenement'=>0,
                'categorie'=>'Mandrill / Mailchimp',
            ],
            [
                'id'=>32,
                'idevenement'=>0,
                'categorie'=>'Charges sociales',
            ],
            [
                'id'=>33,
                'idevenement'=>0,
                'categorie'=>'Mutuelle',
            ],
            [
                'id'=>34,
                'idevenement'=>0,
                'categorie'=>'Assurances',
            ],
            [
                'id'=>35,
                'idevenement'=>0,
                'categorie'=>'Tshirts',
            ],
            [
                'id'=>36,
                'idevenement'=>0,
                'categorie'=>'Captation vidéo',
            ],
            [
                'id'=>37,
                'idevenement'=>0,
                'categorie'=>'Gandi',
            ],
            [
                'id'=>38,
                'idevenement'=>0,
                'categorie'=>'Domiciliation',
            ],
            [
                'id'=>39,
                'idevenement'=>0,
                'categorie'=>'Logistique (stockage, etc.)',
            ],
            [
                'id'=>40,
                'idevenement'=>0,
                'categorie'=>'Standiste',
            ],
            [
                'id'=>41,
                'idevenement'=>0,
                'categorie'=>'Intérêts bancaires',
            ],
            [
                'id'=>42,
                'idevenement'=>0,
                'categorie'=>'Apéro communautaire',
            ],
            [
                'id'=>44,
                'idevenement'=>0,
                'categorie'=>'Diner lancement',
            ],
            [
                'id'=>45,
                'idevenement'=>0,
                'categorie'=>'Meetup',
            ],
            [
                'id'=>46,
                'idevenement'=>0,
                'categorie'=>'Seminaire',
            ],
            [
                'id'=>48,
                'idevenement'=>0,
                'categorie'=>'Outils',
            ],
        ];

        $table = $this->table('compta_categorie');
        $table->truncate();

        $table
            ->insert($data)
            ->save();
    }
}
