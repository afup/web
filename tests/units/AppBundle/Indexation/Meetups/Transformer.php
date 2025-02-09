<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups\tests\units;

use AppBundle\Event\Model\Meetup;
use AppBundle\Indexation\Meetups\Transformer as TestedClass;
use AppBundle\Offices\OfficesCollection;

class Transformer extends \atoum
{
    public function testTransform(): void
    {
        $this
            ->given(
                $id = '244992881',
                $title = 'Apéro PHP',
                $description = 'Nous vous invitons au Grand Comptoir à partir de 18h30 pour discuter de PHP autour d\'un verre.',
                $dateTime = new \DateTime('2050-12-14 17:30:00'),
                $meetupAntenneName = 'reims'
            )
            ->when(
                $meetup = (new Meetup())
                    ->setId($id)
                    ->setDate($dateTime)
                    ->setTitle($title)
                    ->setDescription($description)
                    ->setAntenneName($meetupAntenneName)
            )
            ->when($transformer = new TestedClass(new OfficesCollection()))
            ->then
                ->array($transformer->transform($meetup))
                ->isEqualTo([
                    'meetup_id' => '244992881',
                    'label' => 'Apéro PHP',
                    'event_url' => 'https://www.meetup.com/fr-FR/afup-reims-php/events/244992881',
                    'timestamp' => '2554648200',
                    'year' => '2050',
                    'datetime' => '2050-12-14 17:30:00',
                    'day_month' => '14 Dec',
                    'office' =>
                         [
                            'label' => 'Reims',
                            'logo_url' => '/images/offices/reims.svg',
                        ],
                    'description' => 'Nous vous invitons au Grand Comptoir à partir de 18h30 pour discuter de PHP autour d\'un verre.',
                    'twitter' => 'afup_reims',
                    'custom_sort' => 9223372034300127607,
                    'is_upcoming' => true,
                ])
        ;
    }
}
