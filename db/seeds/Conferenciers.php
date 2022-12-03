<?php

use Phinx\Seed\AbstractSeed;

class Conferenciers extends AbstractSeed
{
    const ID_CONFERENCIER = 1;

    public function run()
    {
        $data = [
            [
                'conferencier_id' => self::ID_CONFERENCIER,
                'id_forum' => Event::ID_FORUM,
                'civilite' => 'M.',
                'nom' => 'Bachelet',
                'prenom' => 'Geoffrey',
                'email' => 'foo@bar.baz',
                'societe' => 'AFUP',
                'ville' => 'Paris',
                'biographie' => 'Président 2018-2019 de l\'AFUP',
                'twitter' => 'ubermuda',
                'user_github' => GithubUsers::ID_GITHUBUSER_UBERMUDA,
                'photo' => '1968.jpg',
                'will_attend_speakers_diner' => null,
                'has_special_diet' => null,
                'special_diet_description' => null,
                'hotel_nights' => null,
            ],
            [
                'conferencier_id' => 2,
                'id_forum' => Event::ID_FORUM,
                'civilite' => 'M.',
                'nom' => 'Gallou',
                'prenom' => 'Adrien',
                'email' => 'foo@bar.baz',
                'societe' => 'AFUP',
                'ville' => 'Paris',
                'biographie' => 'Président pôle outils',
                'twitter' => 'agallou',
                'user_github' => 2,
                'photo' => '1968.jpg',
                'will_attend_speakers_diner' => null,
                'has_special_diet' => null,
                'special_diet_description' => null,
                'hotel_nights' => null,
            ],
        ];

        $table = $this->table('afup_conferenciers');
        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;
    }
}