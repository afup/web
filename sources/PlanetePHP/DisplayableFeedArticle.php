<?php

declare(strict_types=1);

namespace PlanetePHP;

final readonly class DisplayableFeedArticle
{
    public function __construct(
        public ?string $title,
        public ?string $url,
        public ?string $update,
        public ?string $author,
        public ?string $content,
        public ?string $feedName,
        public ?string $feedUrl,
    ) {}
}
