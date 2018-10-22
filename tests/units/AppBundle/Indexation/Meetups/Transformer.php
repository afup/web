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
                                'logo_url' => '/images/offices/reims.svg',
                            ),
                        'description' => '<p>Nous vous invitons au Grand Comptoir à partir de 18h30 pour discuter de PHP autour d\'un verre.</p>',
                        'venue' =>
                            array (
                                'name' => 'Le Grand Comptoir',
                                'address_1' => 'Gare SNCF, 2 Boulevard Louis Roederer',
                                'city' => 'Reims',
                            ),
                        'twitter' => 'afup_reims',
                        'custom_sort' => 9223370523582175807,
                        'is_upcoming' => true,
                    ))
        ;
    }

    public function testMeetupFilter()
    {
        $this
            ->given(
                $meetup = array (
                    'utc_offset' => 3600000,
                    'venue' =>
                        array (
                            'country' => 'fr',
                            'localized_country_name' => 'France',
                            'city' => 'Romans-sur-Isère',
                            'address_1' => '1 rue Claude Bernard',
                            'name' => 'Adequasys',
                            'lon' => 5.0933219999999997,
                            'id' => 25752810,
                            'lat' => 45.045679999999997,
                            'repinned' => false,
                        ),
                    'rsvp_limit' => 20,
                    'headcount' => 0,
                    'visibility' => 'public',
                    'waitlist_count' => 0,
                    'created' => 1519980755000,
                    'maybe_rsvp_count' => 0,
                    'description' => '<p>Au programme<br/>[AFUP] Super Apéro PHP</p> <p>Viens faire connaissance avec l\'AFUP avec les talks dédiés à PHP au sein de ADD.</p> <p>Au programme :<br/>- quizz sur ton langage préféré, en compétition avec toutes les antennes AFUP de France<br/>- présentations des prochaines événements PHP nationaux<br/>- échanges entre développeurs de la région</p> <p>À prendre avec vous</p> <p><br/>Important</p>',
                    'event_url' => 'https://www.meetup.com/Ardech-Drom-Dev/events/248331929/',
                    'yes_rsvp_count' => 9,
                    'duration' => 7200000,
                    'name' => '[AFUP] Super Apéro PHP',
                    'id' => '248331929',
                    'photo_url' => 'https://secure.meetupstatic.com/photos/event/f/c/d/global_468904045.jpeg',
                    'time' => 1520532000000,
                    'updated' => 1519980755000,
                    'group' =>
                        array (
                            'join_mode' => 'open',
                            'created' => 1507261646000,
                            'name' => 'Ardèch’Drôm Dev',
                            'group_lon' => 4.8899998664855957,
                            'id' => 26169682,
                            'urlname' => 'Ardech-Drom-Dev',
                            'group_lat' => 44.930000305175781,
                            'who' => 'Membres',
                        ),
                    'status' => 'upcoming',
                )
            )
            ->when($transformer = new TestedClass(new OfficesCollection()))
            ->then
            ->array($transformer->transform($meetup))
            ->isEqualTo(array (
                'meetup_id' => '248331929',
                'label' => 'Super Apéro PHP',
                'event_url' => 'https://www.meetup.com/Ardech-Drom-Dev/events/248331929/',
                'timestamp' => '1520532000',
                'year' => '2018',
                'datetime' => '2018-03-08 18:00:00',
                'day_month' => '08 Mar',
                'office' => array (
                    'label' => 'Drome Ardèche',
                    'logo_url' => '/images/offices/empty.svg',
                ),
                'description' => '<p>Au programme<br/>[AFUP] Super Apéro PHP</p> <p>Viens faire connaissance avec l\'AFUP avec les talks dédiés à PHP au sein de ADD.</p> <p>Au programme :<br/>- quizz sur ton langage préféré, en compétition avec toutes les antennes AFUP de France<br/>- présentations des prochaines événements PHP nationaux<br/>- échanges entre développeurs de la région</p> <p>À prendre avec vous</p> <p><br/>Important</p>',
                'is_upcoming' => true,
                'custom_sort' => 9223370516322775807,
                'twitter' => 'afup_dromardech',
                'venue' => array (
                    'name' => 'Adequasys',
                    'address_1' => '1 rue Claude Bernard',
                    'city' => 'Romans-sur-Isère',
                ),
            ))
            ->given(
                $meetup = array (
                    'utc_offset' => 3600000,
                    'venue' =>
                        array (
                            'country' => 'fr',
                            'localized_country_name' => 'France',
                            'city' => 'Valence',
                            'address_1' => '51 Rue Barthélémy de Laffemas',
                            'name' => 'IUT Valence',
                            'lon' => 4.9150840000000002,
                            'id' => 25534987,
                            'lat' => 44.915638000000001,
                            'repinned' => false,
                        ),
                    'headcount' => 0,
                    'visibility' => 'public',
                    'waitlist_count' => 0,
                    'created' => 1518256235000,
                    'rating' =>
                        array (
                            'count' => 3,
                            'average' => 5,
                        ),
                    'maybe_rsvp_count' => 0,
                    'description' => '<p>Au programme<br/>== Présentation de Apache Spark</p> <p>Public concerné : développeur tout niveau, statisticiens, n\'importe qui sachant faire du SQL</p> <p>La donnée est un enjeu majeur de notre époque. Beaucoup d\'acteurs ont conscience des opportunités qui se cachent dans leurs données mais ne savent pas les exploiter, les questions principales sont :</p> <p>* quelle finalité ? Trouver des usages pertinents n\'est pas toujours très simple<br/>* quels outils ? Les solutions sont nombreuses, plus ou moins performantes, et il est facile de se sentir perdu<br/>* qui ? Le Data Scientist est aussi rare que le développeur Cobol, où trouver le bon profil ?</p> <p>Spark est la plateforme open-source dominante du moment. Elle a détronée Hadoop et son ecosystème et sa librairie Machine Learning a contribué à son succès.</p> <p>Par ce live-coding Spark, je vous montrerai que la manipulation des données n\'est pas si compliquée. Vous savez faire du SQL ? Vous êtes probablement déjà capable de travailler avec Spark.</p> <p>Quant à l\'usage de vos données, je ne pourrai pas répondre à votre place, mais je présenterai des cas d\'usages qui pourraient vous donner des idées.</p> <p>== Orchestrez vos conteneurs avec Rancher</p> <p>À l\'heure des applications fonctionnent dans des conteneurs Docker, la stratégie mise en place pour le déploiement et la maintenance de celles-ci devient capitale. Rancher est une solution open source permettant de monter en quelques minutes un environnement complet et à la carte; Cattle, Kubernetes, Docker Swarm ou encore Mesos. Nous ferons un tour de toutes les fonctionnalités qu\'il propose puis nous terminerons par la mise en place d\'une stack complète pour terminer l\'exemple.</p> <p>À prendre avec vous</p> <p>Important</p>',
                    'how_to_find_us' => 'Bientôt défini',
                    'event_url' => 'https://www.meetup.com/Ardech-Drom-Dev/events/247677232/',
                    'yes_rsvp_count' => 26,
                    'duration' => 7200000,
                    'name' => 'Présentation Apache Spark et Orchestrez vos conteneurs avec Rancher',
                    'id' => '247677232',
                    'photo_url' => 'https://secure.meetupstatic.com/photos/event/7/7/a/f/global_468330639.jpeg',
                    'time' => 1519754400000,
                    'updated' => 1519801705000,
                    'group' =>
                        array (
                            'join_mode' => 'open',
                            'created' => 1507261646000,
                            'name' => 'Ardèch’Drôm Dev',
                            'group_lon' => 4.8899998664855957,
                            'id' => 26169682,
                            'urlname' => 'Ardech-Drom-Dev',
                            'group_lat' => 44.930000305175781,
                            'who' => 'Membres',
                        ),
                    'status' => 'past',
                )
            )
            ->when($transformer = new TestedClass(new OfficesCollection()))
            ->then
            ->variable($transformer->transform($meetup))
                ->isNull
        ;
    }
}
