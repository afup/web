<?php

namespace AppBundle\Indexation\Meetups\tests\units;

use AppBundle\Indexation\Meetups\Transformer as TestedClass;
use AppBundle\Offices\OfficesCollection;

class Transformer extends \atoum
{
    public function testTransform()
    {
        $this
            ->given(
                $meetup = array (
                    'utc_offset' => 3600000,
                    'venue' =>
                        array (
                            'country' => 'fr',
                            'localized_country_name' => 'France',
                            'city' => 'Reims',
                            'address_1' => 'Gare SNCF, 2 Boulevard Louis Roederer',
                            'name' => 'Le Grand Comptoir',
                            'lon' => 4.0241059999999997,
                            'id' => 25458522,
                            'lat' => 49.258797000000001,
                            'repinned' => false,
                        ),
                    'headcount' => 0,
                    'visibility' => 'public',
                    'waitlist_count' => 0,
                    'created' => 1510311408000,
                    'maybe_rsvp_count' => 0,
                    'description' => '<p>Nous vous invitons au Grand Comptoir à partir de 18h30 pour discuter de PHP autour d\'un verre.</p>',
                    'event_url' => 'https://www.meetup.com/afup-reims-php/events/244992881/',
                    'yes_rsvp_count' => 8,
                    'duration' => 5400000,
                    'name' => 'Apéro PHP',
                    'id' => '244992881',
                    'time' => 1513272600000,
                    'updated' => 1510575232000,
                    'group' =>
                        array (
                            'join_mode' => 'open',
                            'created' => 1491993708000,
                            'name' => 'Antenne AFUP Reims : PHP',
                            'group_lon' => 4.0300002098083496,
                            'id' => 23255694,
                            'urlname' => 'afup-reims-php',
                            'group_lat' => 49.25,
                            'who' => 'Membres',
                        ),
                    'status' => 'upcoming',
                )
            )
            ->when($transformer = new TestedClass(new OfficesCollection()))
            ->then
                ->array($transformer->transform($meetup))
                    ->isEqualTo(array (
                        'meetup_id' => '244992881',
                        'label' => 'Apéro PHP',
                        'event_url' => 'https://www.meetup.com/afup-reims-php/events/244992881/',
                        'timestamp' => '1513272600',
                        'year' => '2017',
                        'datetime' => '2017-12-14 17:30:00',
                        'day_month' => '14 Dec',
                        'office' =>
                            array (
                                'label' => 'Reims',
                                'logo_url' => '/images/offices/reims.jpg',
                            ),
                        'description' => '<p>Nous vous invitons au Grand Comptoir à partir de 18h30 pour discuter de PHP autour d\'un verre.</p>',
                        'venue' =>
                            array (
                                'name' => 'Le Grand Comptoir',
                                'address_1' => 'Gare SNCF, 2 Boulevard Louis Roederer',
                                'city' => 'Reims',
                            ),
                        'custom_sort' => 9223370523582175807,
                        'is_upcoming' => true,
                    ))
        ;
    }
}
