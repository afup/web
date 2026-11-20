<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

/**
 * L'événement d'un talk publié, tel que l'affichent les pages publiques, lu en DBAL.
 */
final readonly class PublishedEvent
{
    public function __construct(
        public int $id,
        public string $title,
        public string $path,
        public ?\DateTimeImmutable $dateStart = null,
        public ?\DateTimeImmutable $dateEnd = null,
    ) {}
}
