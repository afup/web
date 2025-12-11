<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class Conferenciers extends AbstractSeed
{
    public const ID_CONFERENCIER = 1;

    public function run(): void
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
                'mastodon' => 'https://phpc.social/@ubermuda',
                'bluesky' => 'afup.org',
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
                'biographie' => 'Bio Adrien',
                'twitter' => 'agallou',
                'mastodon' => 'https://phpc.social/@agallou',
                'bluesky' => 'lyon.afup.org',
                'user_github' => 2,
                'photo' => '1968.jpg',
                'will_attend_speakers_diner' => null,
                'has_special_diet' => null,
                'special_diet_description' => null,
                'hotel_nights' => null,
            ],
            [
                'conferencier_id' => 3,
                'id_forum' => Event::ID_FORUM,
                'civilite' => 'M.',
                'nom' => 'Doe',
                'prenom' => 'John',
                'email' => 'john.do@bar.baz',
                'societe' => 'A company',
                'ville' => 'Paris',
                'biographie' => 'Bio John',
                'twitter' => '',
                'mastodon' => '',
                'bluesky' => '',
                'user_github' => 3,
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
