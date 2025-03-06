<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Model;

use AppBundle\Event\Model\Talk;
use PHPUnit\Framework\TestCase;

final class TalkTest extends TestCase
{
    public function testYoutubeUrl(): void
    {
        $talk = new Talk();

        self::assertNull($talk->getYoutubeUrl());

        $talk->setYoutubeId("bWi9h2PmBn0");

        self::assertEquals("https://www.youtube.com/watch?v=bWi9h2PmBn0", $talk->getYoutubeUrl());
    }

    public function testSlug(): void
    {
        $talk = new Talk();
        $talk->setTitle('Utiliser PostgreSQL en 2014');

        self::assertEquals('utiliser-postgresql-en-2014', $talk->getSlug());
    }
}
