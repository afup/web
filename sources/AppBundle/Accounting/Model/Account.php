<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use DateTime;

class Account implements NotifyPropertyInterface
{
    use NotifyProperty;

    private ?int $id = null;

    private ?string $name = null;

    private ?DateTime $archivedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->propertyChanged('name', $this->name, $name);
        $this->name = $name;

        return $this;
    }

    public function getArchivedAt(): ?DateTime
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(?DateTime $archivedAt): self
    {
        $this->propertyChanged('archivedAt', $this->archivedAt, $archivedAt);
        $this->archivedAt = $archivedAt;

        return $this;
    }
}
