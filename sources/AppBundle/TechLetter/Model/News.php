<?php

declare(strict_types=1);

namespace AppBundle\TechLetter\Model;

class News implements \JsonSerializable
{
    public function __construct(
        private readonly string $url,
        private readonly string $title,
        private readonly \DateTimeImmutable $date,
    ) {
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
            'date' => $this->date->format('Y-m-d'),
        ];
    }
}
