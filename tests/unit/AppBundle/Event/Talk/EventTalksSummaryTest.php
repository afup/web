<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Talk;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Talk\EventTalksSummary;
use PHPUnit\Framework\TestCase;

final class EventTalksSummaryTest extends TestCase
{
    public function testGroupByYearKeepsTheGivenOrder(): void
    {
        $forum2025 = self::summary('forumphp2025', '2025-10-09');
        $day2025 = self::summary('afupday2025', '2025-05-16');
        $forum2024 = self::summary('forumphp2024', '2024-10-10');

        self::assertSame(
            [2025 => [$forum2025, $day2025], 2024 => [$forum2024]],
            EventTalksSummary::groupByYear([$forum2025, $day2025, $forum2024]),
        );
        self::assertSame([], EventTalksSummary::groupByYear([]));
    }

    private static function summary(string $path, string $dateStart): EventTalksSummary
    {
        return new EventTalksSummary(new Event()->setPath($path)->setDateStart(new \DateTime($dateStart)), 1, 0);
    }
}
