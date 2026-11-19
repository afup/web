<?php

declare(strict_types=1);

namespace AppBundle\Tests\Association\UserMembership;

use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\SeniorityComputer;
use AppBundle\MembershipFee\Entity\Cotisation;
use AppBundle\MembershipFee\Entity\Repository\CotisationRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

final class SeniorityComputerTest extends TestCase
{
    /**
     * Périodes [début, fin] triées par date de fin décroissante, comme les renvoie le repository.
     *
     * @return iterable<string, array{now: string, periodes: list<array{string, string}>, expectedYears: int, expectedFirstYear: ?int}>
     */
    public static function cotisationsProvider(): iterable
    {
        yield 'Aucune cotisation' => [
            'now' => '2027-03-09 12:00:00',
            'periodes' => [],
            'expectedYears' => 0,
            'expectedFirstYear' => null,
        ];

        yield 'Une seule cotisation dans le passé' => [
            'now' => '2026-11-19 12:00:00',
            'periodes' => [
                ['2020-09-10', '2021-09-10'],
            ],
            'expectedYears' => 1,
            'expectedFirstYear' => 2020,
        ];

        // Une cotisation en cours compte comme de l'ancienneté dès le premier jour
        yield 'Une seule cotisation en cours' => [
            'now' => '2021-08-06 12:00:00',
            'periodes' => [
                ['2020-09-10', '2021-09-10'],
            ],
            'expectedYears' => 1,
            'expectedFirstYear' => 2020,
        ];

        yield 'Plusieurs cotisations à la suite sans interruption' => [
            'now' => '2026-11-19 12:00:00',
            'periodes' => [
                ['2023-09-10', '2024-09-10'],
                ['2022-09-10', '2023-09-10'],
                ['2021-09-10', '2022-09-10'],
                ['2020-09-10', '2021-09-10'],
            ],
            'expectedYears' => 4,
            'expectedFirstYear' => 2020,
        ];

        yield 'Une cotisation dans le futur ne compte pas dans le total' => [
            'now' => '2023-08-06 12:00:00',
            'periodes' => [
                ['2023-09-10', '2024-09-10'],
                ['2022-09-10', '2023-09-10'],
                ['2021-09-10', '2022-09-10'],
                ['2020-09-10', '2021-09-10'],
            ],
            'expectedYears' => 3,
            'expectedFirstYear' => 2020,
        ];

        yield 'Pause entre deux cotisations' => [
            'now' => '2026-11-19 12:00:00',
            'periodes' => [
                ['2023-09-10', '2024-09-10'],
                // Pause d'une année ici
                ['2021-09-10', '2022-09-10'],
                ['2020-09-10', '2021-09-10'],
            ],
            'expectedYears' => 3,
            'expectedFirstYear' => 2020,
        ];

        yield 'Cotisation qui démarre avant la fin de la précédente' => [
            'now' => '2026-11-19 12:00:00',
            'periodes' => [
                ['2023-09-05', '2024-09-05'], // Cette cotisation démarre avant la précédente.
                ['2022-09-10', '2023-09-10'],
                ['2021-09-10', '2022-09-10'],
                ['2020-09-10', '2021-09-10'],
            ],
            'expectedYears' => 4,
            'expectedFirstYear' => 2020,
        ];

        yield 'Date du jour 1 seconde avant la date de fin de la dernière cotisation' => [
            'now' => '2024-09-09 23:59:59',
            'periodes' => [
                ['2023-09-10', '2024-09-10'],
                ['2022-09-10', '2023-09-10'],
                ['2021-09-10', '2022-09-10'],
                ['2020-09-10', '2021-09-10'],
            ],
            'expectedYears' => 4,
            'expectedFirstYear' => 2020,
        ];

        yield 'Date du jour égal à la date de fin de la dernière cotisation' => [
            'now' => '2024-09-10 00:00:00',
            'periodes' => [
                ['2023-09-10', '2024-09-10'],
                ['2022-09-10', '2023-09-10'],
                ['2021-09-10', '2022-09-10'],
                ['2020-09-10', '2021-09-10'],
            ],
            'expectedYears' => 4,
            'expectedFirstYear' => 2020,
        ];

        yield 'Date du jour 1 seconde après la date de fin de la dernière cotisation' => [
            'now' => '2024-09-10 00:00:01',
            'periodes' => [
                ['2023-09-10', '2024-09-10'],
                ['2022-09-10', '2023-09-10'],
                ['2021-09-10', '2022-09-10'],
                ['2020-09-10', '2021-09-10'],
            ],
            'expectedYears' => 4,
            'expectedFirstYear' => 2020,
        ];

        // Après une pause, une cotisation payée en ligne commence au lendemain
        // de la fin de la précédente, il ne faut pas compter la pause comme de l'adhésion.
        yield 'Reprise après une pause avec une date de début antidatée' => [
            'now' => '2026-11-19 12:00:00',
            'periodes' => [
                ['2021-09-11', '2024-03-15'],
                ['2020-09-10', '2021-09-10'],
            ],
            'expectedYears' => 2,
            'expectedFirstYear' => 2020,
        ];

        // Certaines anciennes cotisations ont un paiement enregistré deux fois avec les mêmes dates.
        yield 'Même cotisation enregistrée deux fois' => [
            'now' => '2026-11-19 12:00:00',
            'periodes' => [
                ['2017-08-15', '2018-08-15'],
                ['2016-08-15', '2017-08-15'],
                ['2016-08-15', '2017-08-15'],
            ],
            'expectedYears' => 2,
            'expectedFirstYear' => 2016,
        ];
    }

    /**
     * @param list<array{string, string}> $periodes
     */
    #[DataProvider('cotisationsProvider')]
    public function testComputeAndReturnInfos(
        string $now,
        array $periodes,
        int $expectedYears,
        ?int $expectedFirstYear,
    ): void {
        $cotisations = [];
        foreach ($periodes as [$debut, $fin]) {
            $cotisation = new Cotisation();
            $cotisation->dateDebut = new \DateTime($debut . ' 00:00:00');
            $cotisation->dateFin = new \DateTime($fin . ' 00:00:00');
            $cotisations[] = $cotisation;
        }

        $repository = $this->createStub(CotisationRepository::class);
        $repository->method('getListByUserTypeAndId')->willReturn($cotisations);

        $computer = new SeniorityComputer($repository, new MockClock($now, 'Europe/Paris'));

        $user = new User();
        $user->setId(1);

        $infos = $computer->computeAndReturnInfos($user);

        self::assertArrayHasKey('years', $infos);
        self::assertArrayHasKey('first_year', $infos);

        self::assertSame($expectedYears, $infos['years']);
        self::assertSame($expectedFirstYear, $infos['first_year']);
    }
}
