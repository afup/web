<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class Event extends AbstractSeed
{
    public const ID_FORUM = 1;
    public const ID_PASSED = 3;

    public function run(): void
    {
        $now = time();
        $oneDayInSeconds = 60 * 60 * 24;
        $oneMonthInSeconds = $oneDayInSeconds * 30;

        $event = new DateTime('@' . ($now + $oneMonthInSeconds * 5));
        $event->setTime(0, 0, 0);
        $event = $event->getTimestamp();

        $data = [
            [
                'id' => self::ID_FORUM,
                'titre' => 'forum',
                'path' => 'forum',
                'logo_url' => 'http://78.media.tumblr.com/tumblr_lgkqc0mz9d1qfyzelo1_1280.jpg', // oui, c'est un chat
                'nb_places' => 500,
                'date_debut' => date('Y-m-d', $event),
                'date_fin' => date('Y-m-d', $event + $oneDayInSeconds),
                'annee' => date('Y', $event),
                'text' => json_encode([
                    'fr' => 'François le français',
                    'en' => 'Henri l\'anglais',
                    'sponsor_management_fr' => '**Sponsors**, venez, vous serez très visible !',
                    'sponsor_management_en' => '**Sponsors**, come, you will be very visible!',
                    'mail_inscription_content' => 'Contenu email',
                ]),
                'date_fin_appel_projet' => $now + $oneMonthInSeconds,
                'date_debut_appel_conferencier' => $now - $oneMonthInSeconds,
                'date_fin_appel_conferencier' => $event - $oneMonthInSeconds * 2,
                'date_fin_vote' => date('Y-m-d H:i:s', ($event - $oneMonthInSeconds * 2) + $oneDayInSeconds * 7),
                'date_fin_prevente' => $now + $oneMonthInSeconds,
                'date_fin_vente' => $event - $oneDayInSeconds * 7,
                'date_fin_vente_token_sponsor' => $event - $oneDayInSeconds * 7,
                'date_fin_saisie_repas_speakers' => $event - $oneDayInSeconds * 7,
                'date_fin_saisie_nuites_hotel' => $event - $oneDayInSeconds * 7,
                'place_name' => 'Paris',
                'place_address' => 'Marriott Rive Gauche',
                'date_annonce_planning' => $now - $oneMonthInSeconds,
                'transport_information_enabled' => 1,
                'has_prices_defined_with_vat' => 1,
            ],
            [
                'id' => 2,
                'titre' => 'AFUP Day Lyon',
                'path' => 'afup-day-lyon',
                'logo_url' => 'http://78.media.tumblr.com/tumblr_lgkqc0mz9d1qfyzelo1_1280.jpg', // oui, c'est un chat
                'nb_places' => 500,
                'date_debut' => date('Y-m-d', $event),
                'date_fin' => date('Y-m-d', $event + $oneDayInSeconds),
                'annee' => date('Y', $event),
                'text' => json_encode([
                    'fr' => 'François le français',
                    'en' => 'Henri l\'anglais',
                    'sponsor_management_fr' => '**Sponsors**, venez, vous serez très visible !',
                    'sponsor_management_en' => '**Sponsors**, come, you will be very visible!',
                    'mail_inscription_content' => 'Contenu email',
                ]),
                'date_fin_appel_projet' => $now + $oneMonthInSeconds,
                'date_debut_appel_conferencier' => $event - $oneMonthInSeconds,
                'date_fin_appel_conferencier' => $event - $oneMonthInSeconds * 2,
                'date_fin_vote' => date('Y-m-d H:i:s', ($event - $oneMonthInSeconds * 2) + $oneDayInSeconds * 7),
                'date_fin_prevente' => $now + $oneMonthInSeconds,
                'date_fin_vente' => $event - $oneDayInSeconds * 7,
                'date_fin_vente_token_sponsor' => $event - $oneDayInSeconds * 7,
                'date_fin_saisie_repas_speakers' => $event - $oneDayInSeconds * 7,
                'date_fin_saisie_nuites_hotel' => $event - $oneDayInSeconds * 7,
                'place_name' => 'Paris',
                'place_address' => 'Marriott Rive Gauche',
                'date_annonce_planning' => $now - $oneMonthInSeconds,
                'transport_information_enabled' => 1,
                'has_prices_defined_with_vat' => 1,
            ],
            [
                'id' => self::ID_PASSED,
                'titre' => 'Un évènement du passé',
                'path' => 'passed',
                'nb_places' => 100,
                'date_debut' => '2020-05-01',
                'date_fin' => '2020-05-02',
                'annee' => 2000,
                'text' => json_encode([
                    'fr' => 'Un évènement du passé',
                    'en' => 'A past event',
                    'sponsor_management_fr' => '**Sponsors**, venez, vous serez très visible !',
                    'sponsor_management_en' => '**Sponsors**, come, you will be very visible!',
                    'mail_inscription_content' => 'Contenu email de l\'évènement du passé',
                ]),
                'date_fin_appel_projet' => (new DateTime('2020-03-31 00:00:00'))->getTimestamp(),
                'date_debut_appel_conferencier' => (new DateTime('2020-03-15 00:00:00'))->getTimestamp(),
                'date_fin_appel_conferencier' => (new DateTime('2020-02-01 00:00:00'))->getTimestamp(),
                'date_fin_vote' => '2020-03-31 00:00:00',
                'date_fin_prevente' => (new DateTime('2020-04-01 00:00:00'))->getTimestamp(),
                'date_fin_vente' => (new DateTime('2020-04-15 00:00:00'))->getTimestamp(),
                'date_fin_vente_token_sponsor' => (new DateTime('2020-04-01 00:00:00'))->getTimestamp(),
                'date_fin_saisie_repas_speakers' => (new DateTime('2020-04-01 00:00:00'))->getTimestamp(),
                'date_fin_saisie_nuites_hotel' => (new DateTime('2020-04-01 00:00:00'))->getTimestamp(),
                'place_name' => 'Berlin',
                'place_address' => 'rue de Paris',
                'date_annonce_planning' => (new DateTime('2020-04-01 00:00:00'))->getTimestamp(),
                'transport_information_enabled' => 1,
                'has_prices_defined_with_vat' => 1,
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
