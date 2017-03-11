<?php

namespace AppBundle\VideoNotifier\tests\units;

use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use AppBundle\VideoNotifier\TweetGenerator as TestedClass;

class TweetGenerator extends \atoum
{

    /**
     * @dataProvider generateDateProvider
     */
    public function testGenerate($case, $talkInfos, $speakersInfos, $expected)
    {
        $speakers = [];
        foreach ($speakersInfos as $speakersInfo) {
            $speaker = new Speaker();
            $speaker
                ->setFirstname($speakersInfo['firstname'])
                ->setLastname($speakersInfo['lastname'])
                ->setTwitter($speakersInfo['twitter'])
            ;
            $speakers[] = $speaker;
        };
        $this
            ->assert($case)
            ->given(
                $talk = new Talk(),
                $talk->setId($talkInfos['id']),
                $talk->setTitle($talkInfos['title'])
            )
            ->when($generator = new TestedClass())
            ->then
            ->string($generator->generate($talk, $speakers))
                ->isEqualTo($expected, $case)
        ;
    }

    protected function generateDateProvider()
    {
        return [
            [
                'case' => 'Cas général',
                'talk' => [
                    'id' => 1007,
                    'title' => 'Utiliser PostgreSQL en 2014',
                ],
                'speakers' => [
                    [
                        'firstname' => 'Dimitri',
                        'lastname' => 'Fontaine',
                        'twitter' => 'tapoueh',
                    ]
                ],
                'expected' => "Utiliser PostgreSQL en 2014. La conférence de @tapoueh à revoir sur le site de l'AFUP : http://afup.org/talks/1007-utiliser-postgresql-en-2014",
            ],
            [
                'case' => "On change la formulation si le titre + compte twitter sont trop longs. De plus on ne met pas de point si il y  un point d'explamation a la fin du titre",
                'talk' => [
                    'id' => 1941,
                    'title' => 'Notre environnement de développement n’est plus un bizutage !',
                ],
                'speakers' => [
                    [
                        'firstname' => 'Pascal',
                        'lastname' => 'MARTIN',
                        'twitter' => '@pascal_martin',
                    ]
                ],
                'expected' => "Notre environnement de développement n’est plus un bizutage ! Par @pascal_martin à revoir sur le site de l'AFUP : http://afup.org/talks/1941-notre-environnement-de-developpement-n-est-plus-un-bizutage",
            ],
            [
                'case' => "On ne met pas de point si point d'interogation à la fin + pas de twitter",
                'talk' => [
                    'id' => 1941,
                    'title' => 'Peut-on s’affranchir de SonataAdminBundle ?',
                ],
                'speakers' => [
                    [
                        'firstname' => 'Suzanne',
                        'lastname' => 'Favot',
                        'twitter' => null,
                    ]
                ],
                'expected' => "Peut-on s’affranchir de SonataAdminBundle ? La conférence de Suzanne Favot à revoir sur le site de l'AFUP : http://afup.org/talks/1941-peut-on-s-affranchir-de-sonataadminbundle",
            ],


            [
                'case' => "Deux speakers sans tweet a raccourcir",
                'talk' => [
                    'id' => 1903,
                    'title' => "Publier des domain events sans RabbitMQ, c'est possible !",
                ],
                'speakers' => [
                    [
                        'firstname' => 'Simon',
                        'lastname' => 'Delicata',
                        'twitter' => null,
                    ],
                    [
                        'firstname' => 'Julien',
                        'lastname' => 'Salleyron',
                        'twitter' => null,
                    ]
                ],
                'expected' => "Publier des domain events sans RabbitMQ, c'est possible ! Par Simon Delicata et Julien Salleyron à revoir sur http://afup.org/talks/1903-publier-des-domain-events-sans-rabbitmq-c-est-possible",
            ],

            [
                'case' => "Titre long et deux speakers",
                'talk' => [
                    'id' => 1609,
                    'title' => "Comment Ansible et Docker changent notre environnement de mise en production",
                ],
                'speakers' => [
                    [
                        'firstname' => 'Simon',
                        'lastname' => 'Constans',
                        'twitter' => null,
                    ],
                    [
                        'firstname' => '',
                        'lastname' => '',
                        'twitter' => '@maxthoon',
                    ]
                ],
                'expected' => "Comment Ansible et Docker changent notre environnement de mise en production. Par Simon Constans et @maxthoon sur http://afup.org/talks/1609-comment-ansible-et-docker-changent-notre-environnement-de-mise-en-production",
            ],
            [
                'case' => "On évite d'avoir deux espaces à la suite",
                'talk' => [
                    'id' => 1295,
                    'title' => 'Et si on utilisait WordPress ?',
                ],
                'speakers' => [
                    [
                        'firstname' => '',
                        'lastname' => '',
                        'twitter' => "@BoiteAWeb ", // espace volontaire
                    ]
                ],
                'expected' => "Et si on utilisait WordPress ? La conférence de @BoiteAWeb à revoir sur le site de l'AFUP : http://afup.org/talks/1295-et-si-on-utilisait-wordpress",

            ]
        ];
    }

    public function testGenerateWithFullEntities()
    {
        $this
            ->given(
                $talk = new Talk(),
                $talk
                    ->setId(1007)
                    ->setForumId(10)
                    ->setSubmittedOn(new \DateTime("2016-01-06 00:00:00.000000"))
                    ->setTitle('Utiliser PostgreSQL en 2014')
                    ->setAbstract('<p>&Agrave; l\'heure o&ugrave; le NoSQL passe de mode doucement, il est temps de se poser les bonnes questions vis &agrave; vis des technologies de bases de donn&eacute;es &agrave; utiliser, comment et pourquoi.  PostgreSQL entre de plein droit dans la case des SGBD relationnels classiques, aussi nous commencerons par &eacute;tudier ce que de ces outils apportent.  Puis nous ferons le tour des fonctionnalit&eacute;s avanc&eacute;es de PostgreSQL, qui le positionnent comme un &eacute;l&eacute;ment cl&eacute; de votre architecture d\'application.</p>')
                    ->setScheduled(true)
                    ->setType(1)
                    ->setSkill(0)
                    ->setNeedsMentoring(false)
                    ->setYoutubeId('hzn0ODTMNDk')
                    ->setSlidesUrl('http://tapoueh.org/images/confs/PHPTour_2014_PostgreSQL.pdf')
                    ->setJoindinId(11214)
                    ->setBlogPostUrl('http://tapoueh.org/confs/2014/06/23-PHPTour-Lyon-2014')
                    ->setLanguageCode('fr')
            )
            ->and(
                $speaker = new Speaker(),
                $speaker
                    ->setId(800)
                    ->setEventId(10)
                    ->setUser(0)
                    ->setCivility('M.')
                    ->setFirstname('Dimitri')
                    ->setLastname('Fontaine')
                    ->setEmail('example@example.fr')
                    ->setCompany('2nbQuadrant')
                    ->setBiography('Dimitri Fontaine travaille sur PostgreSQL, à la fois en tant que Contributeur Majeur du projet au sein d\'une communauté vibrante et en tant qu\'expert PostgreSQL pour 2ndQuadrant.')
                    ->setTwitter('tapoueh')
            )
            ->and($speakers = [$speaker])
            ->when($generator = new TestedClass())
            ->then
                ->string($generator->generate($talk, $speakers))
                    ->isEqualTo("Utiliser PostgreSQL en 2014. La conférence de @tapoueh à revoir sur le site de l'AFUP : http://afup.org/talks/1007-utiliser-postgresql-en-2014")
        ;
    }
}
