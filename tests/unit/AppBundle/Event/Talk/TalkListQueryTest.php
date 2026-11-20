<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Talk;

use AppBundle\Event\Talk\TalkListQuery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TalkListQueryTest extends TestCase
{
    /**
     * @param array<string, mixed> $query
     */
    #[DataProvider('indexableQueries')]
    public function testIsIndexable(array $query): void
    {
        self::assertTrue(new TalkListQuery($query)->isIndexable());
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function indexableQueries(): iterable
    {
        yield 'liste nue' => [[]];
        yield 'facette speaker' => [['fR' => ['speakers.label' => ['Geoffrey BACHELET']]]];
        yield 'facette événement' => [['fR' => ['event.title' => ['forum']]]];
        yield 'recherche vide' => [['q' => '', 'fR' => ['event.title' => ['forum']]]];
        yield 'première page' => [['p' => '0']];
        yield 'page négative' => [['p' => '-1']];
        yield 'paramètre de suivi' => [['utm_source' => 'newsletter']];
    }

    /**
     * @param array<string, mixed> $query
     */
    #[DataProvider('notIndexableQueries')]
    public function testIsNotIndexable(array $query): void
    {
        self::assertFalse(new TalkListQuery($query)->isIndexable());
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function notIndexableQueries(): iterable
    {
        yield 'recherche' => [['q' => 'symfony']];
        yield 'recherche tableau' => [['q' => ['symfony']]];
        yield 'deuxième page' => [['p' => '1']];
        yield 'page tableau' => [['p' => ['1']]];
        yield 'deux facettes' => [['fR' => ['speakers.label' => ['Geoffrey BACHELET'], 'event.title' => ['forum']]]];
        yield 'deux valeurs de facette' => [['fR' => ['speakers.label' => ['Geoffrey BACHELET', 'Adrien GALLOU']]]];
        yield 'autre facette' => [['fR' => ['has_video' => ['true']]]];
        yield 'facette non tableau' => [['fR' => 'forum']];
        yield 'hPP' => [['hPP' => '7']];
        yield 'idx' => [['idx' => 'afup_talks']];
        yield 'is_v' => [['is_v' => '1']];
        yield 'dFR' => [['dFR' => ['type.label' => ['Conférence']]]];
        yield 'nR' => [['nR' => ['event.start_date' => ['>=' => ['0']]]]];
        yield 'fE' => [['fE' => ['type.label' => ['Atelier']]]];
        yield 'hFR' => [['hFR' => ['category' => ['php']]]];
        yield 'tR' => [['tR' => 'php']];
    }

    public function testGetFacetValues(): void
    {
        $query = new TalkListQuery(['fR' => [
            'speakers.label' => ['Geoffrey BACHELET', ['tableau']],
            'event.title' => 'forum',
        ]]);

        self::assertSame(['Geoffrey BACHELET', ''], $query->getFacetValues(TalkListQuery::FACET_SPEAKER));
        self::assertSame(['forum'], $query->getFacetValues(TalkListQuery::FACET_EVENT));
        self::assertSame([], new TalkListQuery([])->getFacetValues(TalkListQuery::FACET_SPEAKER));
    }
}
