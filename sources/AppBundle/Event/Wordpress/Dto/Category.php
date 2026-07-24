<?php

declare(strict_types=1);

namespace AppBundle\Event\Wordpress\Dto;

final readonly class Category
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
    ) {}
}
