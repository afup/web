<?php

declare(strict_types=1);

namespace AppBundle\TechLetter\Model;

class Article implements \JsonSerializable
{
    private string $url;
    private string $title;
    private string $host;

    /** @var numeric-string */
    private string $readingTime;

    private string $excerpt;
    private string $language;

    public function __construct(string $url, string $title, string $host, string $readingTime, string $excerpt, string $language)
    {
        $this->url = $url;
        $this->title = $title;
        $this->host = $host;
        $this->readingTime = $readingTime;
        $this->excerpt = $excerpt;
        $this->language = $language;
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
