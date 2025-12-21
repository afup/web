<?php

declare(strict_types=1);

namespace AppBundle\Site\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class Country implements NotifyPropertyInterface
{
    use NotifyProperty;

    private string $id = '';

    private string $name = '';

    public function getId()
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->propertyChanged('name', $this->name, $name);
        $this->name = $name;

        return $this;
    }
}
