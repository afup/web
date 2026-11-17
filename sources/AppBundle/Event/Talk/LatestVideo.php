<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

/**
 * Talk publié avec vidéo, tel qu'affiché dans le bloc « Dernières vidéos » de la home.
 * Objet simple pour pouvoir être mis en cache.
 */
final readonly class LatestVideo
{
    public function __construct(
        public int $talkId,
        public string $slug,
        public string $title,
        public string $youtubeId,
        public string $eventTitle,
    ) {}

    public function getThumbnailUrl(): string
    {
        return 'https://img.youtube.com/vi/' . rawurlencode($this->youtubeId) . '/hqdefault.jpg';
    }
}
