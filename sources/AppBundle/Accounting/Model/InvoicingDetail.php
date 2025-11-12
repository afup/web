<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class InvoicingDetail implements NotifyPropertyInterface
{
    use NotifyProperty;

    private ?int $id = null;
    private ?int $invoicingId = null;
    private ?string $reference = null;
    private ?string $designation = null;
    private ?float $quantity = null;
    private ?float $unitPrice = null;
    private ?float $tva = null;

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

    public function getInvoicingId(): ?int
    {
        return $this->invoicingId;
    }

    public function setInvoicingId(?int $invoicingId): self
    {
        $this->propertyChanged('invoicingId', $this->invoicingId, $invoicingId);
        $this->invoicingId = $invoicingId;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $ref): self
    {
        $this->propertyChanged('reference', $this->reference, $ref);
        $this->reference = $ref;

        return $this;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(?string $designation): self
    {
        $this->propertyChanged('designation', $this->designation, $designation);
        $this->designation = $designation;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(?float $quantity): self
    {
        $this->propertyChanged('quantity', $this->quantity, $quantity);
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(?float $unitPrice): self
    {
        $this->propertyChanged('unitPrice', $this->unitPrice, $unitPrice);
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(?float $tva): self
    {
        $this->propertyChanged('tva', $this->tva, $tva);
        $this->tva = $tva;

        return $this;
    }
}
