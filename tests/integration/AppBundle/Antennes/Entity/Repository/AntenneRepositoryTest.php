<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Antennes\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Antennes\Entity\Antenne;
use AppBundle\Antennes\Entity\Repository\AntenneRepository;
use AppBundle\Antennes\AncrageLegende;

final class AntenneRepositoryTest extends IntegrationTestCase
{
    public function testGetAll(): void
    {
        $antennes = $this->repository()->getAll();

        self::assertCount(16, $antennes);
        foreach ($antennes as $code => $antenne) {
            self::assertSame($code, $antenne->code);
        }
    }

    public function testFindByCode(): void
    {
        $lorraine = $this->repository()->findByCode('lorraine');

        self::assertSame('Lorraine', $lorraine->libelle);
        self::assertSame('/images/offices/lorraine.svg', $lorraine->urlLogo);
        self::assertSame('Grand Est', $lorraine->region);
        self::assertSame(['54', '55', '57', '88'], $lorraine->departements);
        self::assertNull($lorraine->pays);
        self::assertFalse($lorraine->masqueePageAntennes);

        self::assertNotNull($lorraine->meetup);
        self::assertSame('afup-lorraine-php', $lorraine->meetup->nomUrl);
        self::assertSame('26854931', $lorraine->meetup->id);

        self::assertSame('AFUP_Lorraine', $lorraine->reseauxSociaux->twitter);
        self::assertSame('lorraine.afup.org', $lorraine->reseauxSociaux->bluesky);
        self::assertNull($lorraine->reseauxSociaux->discord);

        self::assertNotNull($lorraine->carte);
        self::assertFalse($lorraine->carte->utiliserSecondeCouleur);
        self::assertSame(AncrageLegende::Gauche, $lorraine->carte->ancrageLegende);
        self::assertSame(630, $lorraine->carte->premiereVille->premierPoint->x);
        self::assertSame(48.6921, $lorraine->carte->premiereVille->position->latitude);
        self::assertNotNull($lorraine->carte->secondeVille);
        self::assertSame(6.1757, $lorraine->carte->secondeVille->position->longitude);

        $tours = $this->repository()->findByCode('tours');
        self::assertSame(47.380001068115234, $tours->carte?->premiereVille->position->latitude);

        self::assertSame(['lux'], $this->repository()->findByCode('luxembourg')->pays);
    }

    public function testAntenneSansMeetupNiCarte(): void
    {
        $clermont = $this->repository()->findByCode('clermont');

        self::assertTrue($clermont->masqueePageAntennes);
        self::assertNull($clermont->meetup);
        self::assertNull($clermont->carte);
        self::assertNull($clermont->departements);
        self::assertNull($clermont->reseauxSociaux->twitter);
    }

    public function testFindByCodeUnknown(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Antenne introuvable via le code inconnue');

        $this->repository()->findByCode('inconnue');
    }

    public function testGetAllSortedByLabels(): void
    {
        $antennes = $this->repository()->getAllSortedByLabels();

        self::assertArrayNotHasKey('clermont', $antennes);
        self::assertCount(15, $antennes);

        $labels = array_map(static fn(Antenne $antenne): string => $antenne->libelle, array_values($antennes));
        $sorted = $labels;
        sort($sorted, SORT_STRING);
        self::assertSame($sorted, $labels);

        self::assertSame(
            ['marseille' => 'Aix-Marseille', 'bordeaux' => 'Bordeaux', 'lille' => 'Hauts de France'],
            array_slice($this->repository()->getOrderedLabelsByKey(), 0, 3),
        );
    }

    private function repository(): AntenneRepository
    {
        return self::getContainer()->get(AntenneRepository::class);
    }
}
