<?php

declare(strict_types=1);

namespace AppBundle\Tests\Pagination;

use AppBundle\Pagination\SeoPagination;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SeoPaginationTest extends TestCase
{
    /**
     * @return iterable<string, array{int, int, int}>
     */
    public static function lastPageProvider(): iterable
    {
        yield 'aucun élément' => [0, 5, 1];
        yield 'une page pleine' => [5, 5, 1];
        yield 'page incomplète en fin de liste' => [16, 5, 4];
        yield 'pages pleines' => [15, 5, 3];
    }

    #[DataProvider('lastPageProvider')]
    public function testGetLastPageCountsAnIncompleteLastPage(int $numberOfItems, int $itemsPerPage, int $expected): void
    {
        self::assertSame($expected, SeoPagination::getLastPage($numberOfItems, $itemsPerPage));
    }

    public function testEveryPageIsLinkedUpToTenPages(): void
    {
        self::assertSame(['before' => [], 'around' => [1], 'after' => []], SeoPagination::getPages(1, 1));
        self::assertSame(['before' => [], 'around' => range(1, 10), 'after' => []], SeoPagination::getPages(7, 10));
    }

    public function testLinksTheEndsTheSiblingsAndMilestones(): void
    {
        self::assertSame(
            ['before' => [], 'around' => [1, 2, 3, 4, 5], 'after' => [9, 17, 41, 80, 120, 144, 152, 163]],
            SeoPagination::getPages(1, 163),
        );
        self::assertSame(
            [
                'before' => [1, 3, 7, 17, 35, 52, 63, 66],
                'around' => [70, 71, 72, 73, 74, 75, 76, 77, 78],
                'after' => [81, 92, 109, 127, 137, 141, 148],
            ],
            SeoPagination::getPages(74, 148),
        );
        self::assertSame(
            ['before' => [1, 7, 14, 36, 72, 108, 129, 136], 'around' => [144, 145, 146, 147, 148], 'after' => []],
            SeoPagination::getPages(148, 148),
        );
    }

    public function testMilestonesNextToTheSiblingsJoinTheirGroup(): void
    {
        self::assertSame(
            ['before' => [], 'around' => [1, 2, 3, 4, 5, 6, 7, 8], 'after' => [12]],
            SeoPagination::getPages(3, 12),
        );
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function crawlDepthProvider(): iterable
    {
        yield '11 pages' => [11];
        yield '148 pages (actualités en novembre 2026)' => [148];
        yield '203 pages' => [203];
    }

    #[DataProvider('crawlDepthProvider')]
    public function testEveryPageIsAtMostThreeClicksFromTheFirstOne(int $lastPage): void
    {
        $clicks = [1 => 0];
        $queue = [1];
        while ([] !== $queue) {
            $page = array_shift($queue);
            foreach (array_merge(...array_values(SeoPagination::getPages($page, $lastPage))) as $linkedPage) {
                if (!isset($clicks[$linkedPage])) {
                    $clicks[$linkedPage] = $clicks[$page] + 1;
                    $queue[] = $linkedPage;
                }
            }
        }

        self::assertCount($lastPage, $clicks);
        self::assertLessThanOrEqual(3, max($clicks));
    }
}
