<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Indexation\Meetups;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Antennes\Entity\Repository\AntenneRepository;
use AppBundle\Event\Entity\Meetup;
use AppBundle\Indexation\Meetups\Transformer;

final class TransformerTest extends IntegrationTestCase
{
    public function testTransform(): void
    {
        $transformer = new Transformer(self::getContainer()->get(AntenneRepository::class));

        $meetup = new Meetup();
        $meetup->id = 244992881;
        $meetup->date = new \DateTimeImmutable('2050-12-14 17:30:00');
        $meetup->titre = 'Apéro PHP';
        $meetup->description = "Nous vous invitons au Grand Comptoir à partir de 18h30 pour discuter de PHP autour d'un verre.";
        $meetup->codeAntenne = 'reims';

        $result = $transformer->transform($meetup);

        self::assertEquals(
            [
                'meetup_id' => '244992881',
                'label' => 'Apéro PHP',
                'event_url' => 'https://www.meetup.com/fr-FR/afup-reims-php/events/244992881',
                'timestamp' => '2554648200',
                'year' => '2050',
                'datetime' => '2050-12-14 17:30:00',
                'day_month' => '14 Dec',
                'office' => [
                    'label' => 'Reims',
                    'logo_url' => '/images/offices/reims.svg',
                ],
                'description' => '<p>Nous vous invitons au Grand Comptoir à partir de 18h30 pour discuter de PHP autour d\'un verre.</p>',
                'twitter' => 'afup_reims',
                'custom_sort' => 9223372034300127607,
                'is_upcoming' => true,
            ],
            $result,
        );
    }
}
