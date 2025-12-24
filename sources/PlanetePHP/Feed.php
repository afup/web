<?php

declare(strict_types=1);

namespace PlanetePHP;

final readonly class Feed
{
    public function __construct(
        public int $id,
        public string $name,
        public string $url,
        public string $feed,
        public FeedStatus $status,
        public ?int $userId,
    ) {}
}
