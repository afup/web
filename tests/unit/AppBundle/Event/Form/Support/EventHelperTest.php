<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Form\Support;

use AppBundle\Event\Form\Support\EventHelper;
use AppBundle\Event\Model\Event;
use AppBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class EventHelperTest extends TestCase
{
    #[DataProvider('groupByYearDataProvider')]
    public function testGroupByYear(Event|string $event, string $expectedYear): void
    {
        $helper = new EventHelper();

        $actualYear = $helper->groupByYear($event);

        self::assertEquals($expectedYear, $actualYear);
    }

    public static function groupByYearDataProvider(): \Generator
    {
        yield 'with event as string' => [
            'event' => 'Forum PHP 2014',
            'expectedYear' => '2014',
        ];

        yield 'with start date' => [
            'event' => (new Event())->setDateStart(new \DateTime('1999-12-13')),
            'expectedYear' => '1999',
        ];

        yield 'with date in title' => [
            'event' => (new Event())->setTitle('AFUP Day 2025 Lyon'),
            'expectedYear' => '2025',
        ];

        yield 'without date in title' => [
            'event' => (new Event())
                ->setTitle('PHP Tour'),
            'expectedYear' => 'Année inconnue',
        ];

        yield 'without date' => [
            'event' => (new Event()),
            'expectedYear' => 'Année inconnue',
        ];
    }

    public function testSortEventsByStartDate(): void
    {
        $events = [
            (new Event())->setDateStart(new \DateTime('2020-05-03')),
            (new Event())->setDateStart(new \DateTime('2021-05-03')),
            (new Event())->setDateStart(new \DateTime('2022-05-03')),
            (new Event())->setDateStart(new \DateTime('2023-05-03')),
            (new Event())->setDateStart(new \DateTime('2024-05-03')),
        ];

        $helper = new EventHelper();

        $r = $helper->sortEventsByStartDate(self::faker()->shuffle($events));

        self::assertCount(5, $r);
        self::assertEquals('2024-05-03', $r[0]->getDateStart()->format('Y-m-d'));
        self::assertEquals('2023-05-03', $r[1]->getDateStart()->format('Y-m-d'));
        self::assertEquals('2022-05-03', $r[2]->getDateStart()->format('Y-m-d'));
        self::assertEquals('2021-05-03', $r[3]->getDateStart()->format('Y-m-d'));
        self::assertEquals('2020-05-03', $r[4]->getDateStart()->format('Y-m-d'));
    }
}
