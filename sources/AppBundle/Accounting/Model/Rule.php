<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class Rule implements NotifyPropertyInterface
{
    use NotifyProperty;

    private ?int $id = null;

    private ?string $label = null;
    private ?string $condition = null;

    private ?bool $isCredit = null;

    private ?string $vat = null;

    private ?int $categoryId = null;

    private ?int $eventId = null;

    private ?int $paymentTypeId = null;

    private ?bool $attachmentRequired = null;

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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->propertyChanged('name', $this->label, $label);
        $this->label = $label;

        return $this;
    }

    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function setCondition(?string $condition): self
    {
        $this->propertyChanged('condition', $this->condition, $condition);
        $this->condition = $condition;

        return $this;
    }

    public function getIsCredit(): ?bool
    {
        return $this->isCredit;
    }

    public function setIsCredit(?bool $isCredit): self
    {
        $this->propertyChanged('isCredit', $this->isCredit, $isCredit);
        $this->isCredit = $isCredit;

        return $this;
    }

    public function getpaymentTypeId(): ?int
    {
        return $this->paymentTypeId;
    }

    public function setPaymentTypeId(?int $paymentTypeId): void
    {
        $this->propertyChanged('paymentTypeId', $this->paymentTypeId, $paymentTypeId);
        $this->paymentTypeId = $paymentTypeId;
    }

    public function getVat(): ?string
    {
        return $this->vat;
    }

    public function setVat(?string $vat): self
    {
        $this->propertyChanged('vat', $this->vat, $vat);
        $this->vat = $vat;

        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(?int $categoryId): void
    {
        $this->propertyChanged('categoryId', $this->categoryId, $categoryId);
        $this->categoryId = $categoryId;
    }

    public function getEventId(): ?int
    {
        return $this->eventId;
    }

    public function setEventId(?int $eventId): void
    {
        $this->propertyChanged('eventId', $this->eventId, $eventId);
        $this->eventId = $eventId;
    }

    public function IsAttachmentRequired(): ?bool
    {
        return $this->attachmentRequired;
    }

    public function setAttachmentRequired(?bool $attachmentRequired): self
    {
        $this->propertyChanged('attachmentRequired', $this->attachmentRequired, $attachmentRequired);
        $this->attachmentRequired = $attachmentRequired;

        return $this;
    }
}
