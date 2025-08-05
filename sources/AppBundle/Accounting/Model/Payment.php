<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class Payment implements NotifyPropertyInterface
{
    use NotifyProperty;

    private ?int $id = null;

    private ?string $name = null;

    private ?\DateTime $hideInAccountingJournalAt = null;

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

    public function getHideInAccountingJournalAt(): ?\DateTime
    {
        return $this->hideInAccountingJournalAt;
    }

    public function setHideInAccountingJournalAt(?\DateTime $hideInAccountingJournalAt): void
    {
        $this->propertyChanged('hideInAccountingJournalAt', $this->hideInAccountingJournalAt, $hideInAccountingJournalAt);
        $this->hideInAccountingJournalAt = $hideInAccountingJournalAt;
    }
}
