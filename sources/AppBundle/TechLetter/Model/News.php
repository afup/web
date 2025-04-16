<?php

declare(strict_types=1);

namespace AppBundle\TechLetter\Model;

class News implements \JsonSerializable
{
    private string $url;
    private string $title;
    private \DateTimeImmutable $date;

    public function __construct(string $url, string $title, \DateTimeImmutable $date)
    {
        $this->url = $url;
        $this->title = $title;
        $this->date = $date;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function jsonSerialize(): array
    {
        return [
            'url' => $this->url,
            'title' => $this->title,
            'date' => $this->date->format('Y-m-d')
        ];
    }
}
