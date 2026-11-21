<?php

declare(strict_types=1);

namespace AppBundle\Event\Speaker;

/**
 * Résultat de recherche tous événements confondus : une personne, ou un conférencier non rattaché.
 * $speakerId désigne la ligne la plus récente, celle qu'on reprend.
 */
final readonly class SpeakerSearchResult
{
    public function __construct(
        public int $speakerId,
        public ?int $personId,
        public ?string $personSlug,
        public string $firstname,
        public string $lastname,
        public ?string $company,
        public ?string $email,
        public ?string $lastEventTitle,
        public int $eventCount,
    ) {}
}
