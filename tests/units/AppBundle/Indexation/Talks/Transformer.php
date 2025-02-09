<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Talks\tests\units;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use AppBundle\Indexation\Talks\Transformer as TestedClass;

class Transformer extends \atoum
{
    public function testTransform(): void
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
                $planning = new Planning(),
                $planning
                    ->setId(266)
                    ->setTalkId(1007)
                    ->setEventId(10)
                    ->setIsKeynote(false)
            )
            ->and(
                $event = new Event(),
                $event
                    ->setId(10)
                    ->setTitle("PHP Tour Lyon 2014")
                    ->setSeats(300)
                    ->setDateStart(new \DateTime("2014-06-24"))
                    ->setDateEnd(new \DateTime('2014-06-25'))
                    ->setDateEndCallForProjects(new \DateTime('2013-12-31'))
                    ->setDateEndCallForPapers(new \DateTime("2014-02-28"))
                    ->setDateEndPreSales(new \DateTime('2014-02-15'))
                    ->setDateEndSales(new \DateTime('2014-06-22'))
                    ->setPath('phptourlyon2014')
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
            ->and($speakers = new \ArrayObject([$speaker]))
            ->when($transformer = new TestedClass())
            ->then
                ->array($transformer->transform($planning, $talk, $event, $speakers))
                    ->isEqualTo([
                        'planning_id' => 266,
                        'talk_id' => 1007,
                        'url_key' => '1007-utiliser-postgresql-en-2014',
                        'title' => "Utiliser PostgreSQL en 2014",
                        'event' => [
                            'id' => 10,
                            'title' => 'PHP Tour Lyon 2014',
                            'start_date' => '2014-06-24',
                        ],
                        'type' => [
                            'id' => 1,
                            'label' => 'Conférence (40 minutes)',
                        ],
                        'speakers_label' => 'Dimitri FONTAINE',
                        'speakers' => [
                            [
                                'id' => 800,
                                'first_name' => 'Dimitri',
                                'last_name' => 'Fontaine',
                                'label' => 'Dimitri FONTAINE',
                            ]
                        ],
                        'has_video' => true,
                        'video_url' => 'https://www.youtube.com/watch?v=hzn0ODTMNDk',
                        'video_id' => 'hzn0ODTMNDk',
                        'video_has_fr_subtitles' => false,
                        'video_has_en_subtitles' => false,
                        'has_slides' => true,
                        'slides_url' => 'http://tapoueh.org/images/confs/PHPTour_2014_PostgreSQL.pdf',
                        'has_joindin' => true,
                        'joindin_url' => '/talks/1007-utiliser-postgresql-en-2014/joindin',
                        'has_blog_post' => true,
                        'blog_post_url' => 'http://tapoueh.org/confs/2014/06/23-PHPTour-Lyon-2014',
                        'language' => [
                            'code' => 'fr',
                            'label' => 'Français',
                        ]
                    ])
        ;
    }

    public function testTransformEmpty(): void
    {
        $this
            ->given($talk = new Talk())
                ->and($talk->setLanguageCode('fr'))
                ->and($planning = new Planning())
                ->and($event = new Event())
                ->and($speakers = new \ArrayObject([new Speaker()]))
            ->when($transformer = new TestedClass())
            ->then
                ->array($object = $transformer->transform($planning, $talk, $event, $speakers))
                    ->notHasKey('slides_url')
                    ->notHasKey('joindin_url')
                    ->notHasKey('blog_post_url')
                    ->boolean($object['has_slides'])->isFalse
                    ->boolean($object['has_joindin'])->isFalse
                    ->boolean($object['has_blog_post'])->isFalse
        ;
    }
}
