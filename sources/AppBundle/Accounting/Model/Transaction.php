<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use DateTime;

class Transaction implements NotifyPropertyInterface
{
    use NotifyProperty;

    private ?int $id = null;
    private ?string $idKey = null;
    private ?int $operationId = null;
    private ?int $categoryId = null;
    private ?DateTime $accountingDate = null;
    private ?string $operationNumber = null;
    private string $vendorName = '';
    private ?string $tvaIntra = null;
    private ?string $tvaZone = null;
    private float $amount = 0.0;
    private string $description = '';
    private ?string $comment = null;
    private bool $attachmentRequired = false;
    private ?string $attachmentFilename = null;
    private string $number = '';
    private ?int $paymentTypeId = null;
    private ?DateTime $paymentDate = null;
    private string $paymentComment = '';
    private ?int $eventId = null;
    private ?int $accountId = 1;
    private ?float $amountTva20 = null;
    private ?float $amountTva10 = null;
    private ?float $amountTva5_5 = null;
    private ?float $amountTva0 = null;

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

    public function getIdKey(): ?string
    {
        return $this->idKey;
    }

    public function setIdKey(?string $idKey): self
    {
        $this->propertyChanged('name', $this->idKey, $idKey);
        $this->idKey = $idKey;

        return $this;
    }

    public function getOperationId(): ?int
    {
        return $this->operationId;
    }

    public function setOperationId(?int $operationId): self
    {
        $this->propertyChanged('operationId', $this->operationId, $operationId);
        $this->operationId = $operationId;

        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(?int $categoryId): self
    {
        $this->propertyChanged('categoryId', $this->categoryId, $categoryId);
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getAccountingDate(): ?DateTime
    {
        return $this->accountingDate;
    }

    public function setAccountingDate(?DateTime $accountingDate): self
    {
        $this->propertyChanged('accountingDate', $this->accountingDate, $accountingDate);
        $this->accountingDate = $accountingDate;

        return $this;
    }

    public function getOperationNumber(): ?string
    {
        return $this->operationNumber;
    }

    public function setOperationNumber(?string $operationNumber): self
    {
        $this->propertyChanged('operationNumber', $this->operationNumber, $operationNumber);
        $this->operationNumber = $operationNumber;

        return $this;
    }

    public function getVendorName(): string
    {
        return $this->vendorName;
    }

    public function setVendorName(string $vendorName): self
    {
        $this->propertyChanged('vendorName', $this->vendorName, $vendorName);
        $this->vendorName = $vendorName;

        return $this;
    }

    public function getTvaIntra(): ?string
    {
        return $this->tvaIntra;
    }

    public function setTvaIntra(?string $tvaIntra): self
    {
        $this->propertyChanged('tvaIntra', $this->tvaIntra, $tvaIntra);
        $this->tvaIntra = $tvaIntra;

        return $this;
    }

    public function getTvaZone(): ?string
    {
        return $this->tvaZone;
    }

    public function setTvaZone(?string $tvaZone): self
    {
        $this->propertyChanged('tvaZone', $this->tvaZone, $tvaZone);
        $this->tvaZone = $tvaZone;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->propertyChanged('amount', $this->amount, $amount);
        $this->amount = $amount;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->propertyChanged('description', $this->description, $description);
        $this->description = $description;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->propertyChanged('comment', $this->comment, $comment);
        $this->comment = $comment;

        return $this;
    }

    public function isAttachmentRequired(): bool
    {
        return $this->attachmentRequired;
    }

    public function setAttachmentRequired(bool $attachmentRequired): self
    {
        $this->propertyChanged('attachmentRequired', $this->attachmentRequired, $attachmentRequired);
        $this->attachmentRequired = $attachmentRequired;

        return $this;
    }

    public function getAttachmentFilename(): ?string
    {
        return $this->attachmentFilename;
    }

    public function setAttachmentFilename(?string $attachmentFilename): self
    {
        $this->propertyChanged('attachmentFilename', $this->attachmentFilename, $attachmentFilename);
        $this->attachmentFilename = $attachmentFilename;

        return $this;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->propertyChanged('number', $this->number, $number);
        $this->number = $number;

        return $this;
    }

    public function getPaymentTypeId(): ?int
    {
        return $this->paymentTypeId;
    }

    public function setPaymentTypeId(?int $paymentTypeId): self
    {
        $this->propertyChanged('paymentTypeId', $this->paymentTypeId, $paymentTypeId);
        $this->paymentTypeId = $paymentTypeId;

        return $this;
    }

    public function getPaymentDate(): ?DateTime
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?DateTime $paymentDate): self
    {
        $this->propertyChanged('paymentDate', $this->paymentDate, $paymentDate);
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function getPaymentComment(): string
    {
        return $this->paymentComment;
    }

    public function setPaymentComment(string $paymentComment): self
    {
        $this->propertyChanged('paymentComment', $this->paymentComment, $paymentComment);
        $this->paymentComment = $paymentComment;

        return $this;
    }

    public function getEventId(): ?int
    {
        return $this->eventId;
    }

    public function setEventId(?int $eventId): self
    {
        $this->propertyChanged('eventId', $this->eventId, $eventId);
        $this->eventId = $eventId;

        return $this;
    }

    public function getAccountId(): ?int
    {
        return $this->accountId;
    }

    public function setAccountId(?int $accountId): self
    {
        $this->propertyChanged('accountId', $this->accountId, $accountId);
        $this->accountId = $accountId;

        return $this;
    }

    public function getAmountTva20(): ?float
    {
        return $this->amountTva20;
    }

    public function setAmountTva20(?float $amountTva20): self
    {
        $this->propertyChanged('amountTva20', $this->amountTva20, $amountTva20);
        $this->amountTva20 = $amountTva20;

        return $this;
    }

    public function getAmountTva10(): ?float
    {
        return $this->amountTva10;
    }

    public function setAmountTva10(?float $amountTva10): self
    {
        $this->propertyChanged('amountTva10', $this->amountTva10, $amountTva10);
        $this->amountTva10 = $amountTva10;

        return $this;
    }

    public function getAmountTva55(): ?float
    {
        return $this->amountTva5_5;
    }

    public function setAmountTva55(?float $amountTva5_5): self
    {
        $this->propertyChanged('amountTva55', $this->amountTva5_5, $amountTva5_5);
        $this->amountTva5_5 = $amountTva5_5;

        return $this;
    }

    public function getAmountTva0(): ?float
    {
        return $this->amountTva0;
    }

    public function setAmountTva0(?float $amountTva0): self
    {
        $this->propertyChanged('amountTva0', $this->amountTva0, $amountTva0);
        $this->amountTva0 = $amountTva0;

        return $this;
    }
}
