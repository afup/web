<?php

declare(strict_types=1);

namespace AppBundle\Site\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use DateTime;

class Sheet implements NotifyPropertyInterface
{
    use NotifyProperty;

    private ?int $id = null;

    private ?int $idParent = null;

    private ?string $name = null;

    private ?string $link = null;
    private ?string $alt = null;

    private ?int $position = null;

    private ?DateTime $creationDate = null;
    private ?int $state = null;
    private ?string $image = null;

    private ?string $imageAlt = null;

    private ?string $patterns = null;

    public function __construct()
    {
        $this->creationDate = new DateTime();
        $this->state = 0;
        $this->position = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
    }


    public function getIdParent()
    {
        return $this->idParent;
    }

    public function setIdParent(?int $id): void
    {
        $this->propertyChanged('idParent', $this->idParent, $id);
        $this->idParent = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->propertyChanged('name', $this->name, $name);
        $this->name = $name;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): void
    {
        $this->propertyChanged('link', $this->link, $link);
        $this->link = $link;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): void
    {
        $this->propertyChanged('alt', $this->alt, $alt);
        $this->alt = $alt;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->propertyChanged('position', $this->position, $position);
        $this->position = $position;
    }

    public function getCreationDate(): ?DateTime
    {
        return $this->creationDate;
    }

    public function setCreationDate(?DateTime $creationDate): void
    {
        $this->propertyChanged('creationDate', $this->creationDate, $creationDate);
        $this->creationDate = $creationDate;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(?int $state): void
    {
        $this->propertyChanged('state', $this->state, $state);
        $this->state = $state;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->propertyChanged('image', $this->image, $image);
        $this->image = $image;
    }

    public function getImageAlt(): ?string
    {
        return $this->imageAlt;
    }

    public function setImageAlt(?string $imageAlt): void
    {
        $this->propertyChanged('imageAlt', $this->imageAlt, $imageAlt);
        $this->imageAlt = $imageAlt;
    }

    public function getPatterns(): ?string
    {
        return $this->patterns;
    }

    public function setPatterns(?string $patterns): void
    {
        $this->propertyChanged('patterns', $this->patterns, $patterns);
        $this->patterns = $patterns;
    }
}
