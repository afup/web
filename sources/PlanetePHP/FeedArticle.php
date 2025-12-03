<?php

declare(strict_types=1);

namespace PlanetePHP;

final readonly class FeedArticle
{
    public function __construct(
        public ?int $id,
        public ?int $feedId,
        public ?string $key,
        public ?string $title,
        public ?string $url,
        public ?int $update,
        public ?string $author,
        public ?string $summary,
        public ?string $content,
        public ?int $status,
    ) {}
}
