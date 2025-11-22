<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use DateTime;

class InvoicingPeriod implements NotifyPropertyInterface
{
    use NotifyProperty;

    private ?int $id = null;

    private ?DateTime $startDate = null;
    private ?DateTime $endDate = null;
    private ?bool $locked = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        $this->propertyChanged('id', $this->id, $id);

        return $this;
    }

    public function getStartdate(): ?DateTime
    {
        return $this->startDate;
    }

    public function setStartdate(?DateTime $startDate): self
    {
        $this->startDate = $startDate;
        $this->propertyChanged('startdate', $this->startDate, $startDate);

        return $this;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTime $endDate): self
    {
        $this->endDate = $endDate;
        $this->propertyChanged('endDate', $this->endDate, $endDate);

        return $this;
    }

    public function getLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(?bool $locked): self
    {
        $this->locked = $locked;
        $this->propertyChanged('locked', $this->locked, $locked);

        return $this;
    }
}
