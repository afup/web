<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Model;

use AppBundle\Event\Model\Event;
use DateTime;
use PHPUnit\Framework\TestCase;

final class EventTest extends TestCase
{
    /** @dataProvider dates */
    public function testIsCfpOpen(? DateTime $start, ? DateTime $end, bool $expected): void
    {
        $event = new Event();
        $event->setDateStartCallForPapers($start);
        $event->setDateEndCallForPapers($end);

        self::assertEquals($expected, $event->isCfpOpen());
    }

    public function dates(): \Generator
    {
        yield 'all null' => [null, null, false];
        yield 'null start date' => [null, new DateTime('+1 day'), true];
        yield 'null end date' => [new DateTime('-1 day'), null, false];
        yield 'on time' => [new DateTime('-1 day'), new DateTime('+1 day'), true];
        yield 'on past' => [new DateTime('-2 day'), new DateTime('-1 day'), false];
        yield 'on futur' => [new DateTime('+1 day'), new DateTime('+2 day'), false];
    }
}
