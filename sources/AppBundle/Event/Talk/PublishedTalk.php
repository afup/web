<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

use Cocur\Slugify\Slugify;

/**
 * Un talk publié tel que l'affichent les pages publiques (cartes, listes, JSON-LD), lu en DBAL.
 */
final readonly class PublishedTalk
{
    public string $slug;

    public function __construct(
        public int $id,
        public string $title,
        public ?string $youtubeId = null,
        public ?string $slidesUrl = null,
        public ?string $blogPostUrl = null,
    ) {
        // Le même slug que Talk::getSlug(), qui sert dans l'URL du talk.
        $this->slug = new Slugify()->slugify($title);
    }

    public function hasYoutubeId(): bool
    {
        return null !== $this->youtubeId && '' !== $this->youtubeId;
    }
}
