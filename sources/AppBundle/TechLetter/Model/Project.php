<?php

declare(strict_types=1);

namespace AppBundle\TechLetter\Model;

class Project implements \JsonSerializable
{
    public function __construct(
        private readonly string $url,
        private readonly string $name,
        private readonly string $description,
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function jsonSerialize(): array
    {
        return [
            'url' => $this->url,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
