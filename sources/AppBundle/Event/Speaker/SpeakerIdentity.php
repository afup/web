<?php

declare(strict_types=1);

namespace AppBundle\Event\Speaker;

/**
 * Une personne qui a présenté au moins un talk publié, toutes éditions confondues,
 * avec son slug. Le nom est celui de la ligne speaker la plus récente.
 */
final readonly class SpeakerIdentity
{
    /**
     * @param non-empty-list<int> $speakerIds les lignes speaker (une par édition) de cette personne
     */
    public function __construct(
        public string $slug,
        public string $firstname,
        public string $lastname,
        public array $speakerIds,
        public \DateTimeImmutable $latestTalkAt,
    ) {}

    /**
     * « Prénom Nom », tel que saisi, espaces superflus retirés.
     */
    public function getName(): string
    {
        return self::cleanSpaces($this->firstname . ' ' . $this->lastname);
    }

    /**
     * « Prénom NOM », comme Speaker::getLabel(), espaces superflus retirés.
     */
    public function getLabel(): string
    {
        return self::cleanSpaces($this->firstname . ' ' . mb_strtoupper($this->lastname));
    }

    private static function cleanSpaces(string $value): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $value));
    }
}
