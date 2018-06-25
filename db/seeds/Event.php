<?php

use Phinx\Seed\AbstractSeed;

class Event extends AbstractSeed
{
    const ID_FORUM = 1;

    public function run()
    {
        $now = time();
        $oneDayInSeconds = 60*60*24;
        $oneMonthInSeconds = $oneDayInSeconds*30;
        $event = $now + $oneMonthInSeconds * 5;

        $data = [
            [
                'id' => self::ID_FORUM,
                'titre' => 'forum',
                'path' => 'forum',
                'trello_list_id' => null,
                'logo_url' => 'http://78.media.tumblr.com/tumblr_lgkqc0mz9d1qfyzelo1_1280.jpg', // oui, c'est un chat
                'nb_places' => 500,
                'date_debut' => date('Y-m-d', $event),
                'date_fin' => date('Y-m-d', $event + $oneDayInSeconds),
                'annee' => date('Y', $event),
                'text' => 'Lorem ipsum dolor amet cronut four loko cloud bread, chicharrones salvia chia vice aesthetic edison bulb ugh hashtag kogi venmo. Shaman raclette humblebrag cray tousled. Direct trade cliche keffiyeh small batch mustache. Marfa heirloom mixtape fingerstache deep v. 3 wolf moon keytar unicorn kitsch, pabst biodiesel umami pok pok ugh normcore iPhone tofu squid. Green juice vexillologist edison bulb echo park air plant adaptogen. Everyday carry flexitarian green juice, unicorn leggings mixtape prism knausgaard chambray cray woke helvetica tousled cred.',
                'date_fin_appel_projet' => $now + $oneMonthInSeconds,
                'date_fin_appel_conferencier' => $event - $oneMonthInSeconds * 2,
                'date_fin_vote' => date('Y-m-d H:i:s', ($event - $oneMonthInSeconds * 2) + $oneDayInSeconds * 7),
                'date_fin_prevente' => $now + $oneMonthInSeconds,
                'date_fin_vente' => $event - $oneDayInSeconds * 7,
                'date_fin_saisie_repas_speakers' => $event - $oneDayInSeconds * 7,
                'date_fin_saisie_nuites_hotel' => $event - $oneDayInSeconds * 7,
                'place_name' => 'Paris',
                'place_address' => 'Marriott Rive Gauche'
            ],
        ];

        $table = $this->table('afup_forum');
        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;
    }
}