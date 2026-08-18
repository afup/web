<?php

declare(strict_types=1);

namespace AppBundle\Event\Wordpress\Dto;

final readonly class PersistedInterview
{
    public function __construct(
        public int $id,
        public string $slug,
    ) {}
}
