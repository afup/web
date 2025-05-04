<?php

declare(strict_types=1);

namespace AppBundle\TechLetter\Model;

class Article implements \JsonSerializable
{
    private const DEFAULT_LANGUAGE = 'en';
    private readonly string $language;

    public function __construct(
        private readonly string $url,
        private readonly string $title,
        private readonly string $host,
        private readonly string $readingTime,
        private readonly string $excerpt,
        ?string $language = self::DEFAULT_LANGUAGE,
    ) {
        $this->language = $language ?? self::DEFAULT_LANGUAGE;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getReadingTime(): string
    {
        return $this->readingTime;
    }

    public function getExcerpt(): string
    {
        return $this->excerpt;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function jsonSerialize(): array
    {
        return [
            'url' => $this->url,
            'title' => $this->title,
            'host' => $this->host,
            'readingTime' => $this->readingTime,
            'excerpt' => $this->excerpt,
            'language' => $this->language,
        ];
    }
}
