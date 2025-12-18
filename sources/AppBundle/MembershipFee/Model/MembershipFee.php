<?php

declare(strict_types=1);

namespace AppBundle\MembershipFee\Model;

use AppBundle\Association\MemberType;
use AppBundle\Controller\Admin\Membership\MembershipFeePayment;
use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use DateTime;

class MembershipFee implements NotifyPropertyInterface
{
    use NotifyProperty;

    private ?int $id = null;
    private ?MemberType $userType = null;
    private ?int $userId = null;
    private ?float $amount = null;
    private ?MembershipFeePayment $paymentType = null;
    private ?string $paymentDetails = null;
    private ?DateTime $startDate = null;
    private ?DateTime $endDate = null;
    private ?string $invoiceNumber = null;
    private ?string $clientReference = null;
    private ?string $comments = null;
    private ?string $token = null;
    private ?int $nbReminders = null;
    private ?DateTime $lastReminderDate = null;

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

    public function getUserType(): ?MemberType
    {
        return $this->userType;
    }

    public function setUserType(?MemberType $userType): self
    {
        $this->propertyChanged('userType', $this->userType, $userType);
        $this->userType = $userType;
        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): self
    {
        $this->propertyChanged('userId', $this->userId, $userId);
        $this->userId = $userId;
        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->propertyChanged('amount', $this->amount, $amount);
        $this->amount = $amount;
        return $this;
    }

    public function getPaymentType(): ?MembershipFeePayment
    {
        return $this->paymentType;
    }

    public function setPaymentType(?MembershipFeePayment $paymentType): self
    {
        $this->propertyChanged('paymentType', $this->paymentType, $paymentType);
        $this->paymentType = $paymentType;
        return $this;
    }

    public function getPaymentDetails(): ?string
    {
        return $this->paymentDetails;
    }

    public function setPaymentDetails(?string $paymentDetails): self
    {
        $this->propertyChanged('paymentDetails', $this->paymentDetails, $paymentDetails);
        $this->paymentDetails = $paymentDetails;
        return $this;
    }

    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTime $startDate): self
    {
        $this->propertyChanged('startDate', $this->startDate, $startDate);
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTime $endDate): self
    {
        $this->propertyChanged('endDate', $this->endDate, $endDate);
        $this->endDate = $endDate;
        return $this;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(?string $invoiceNumber): self
    {
        $this->propertyChanged('invoiceNumber', $this->invoiceNumber, $invoiceNumber);
        $this->invoiceNumber = $invoiceNumber;
        return $this;
    }

    public function getClientReference(): ?string
    {
        return $this->clientReference;
    }

    public function setClientReference(?string $clientReference): self
    {
        $this->propertyChanged('clientReference', $this->clientReference, $clientReference);
        $this->clientReference = $clientReference;
        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): self
    {
        $this->propertyChanged('comments', $this->comments, $comments);
        $this->comments = $comments;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->propertyChanged('token', $this->token, $token);
        $this->token = $token;
        return $this;
    }

    public function getNbReminders(): ?int
    {
        return $this->nbReminders;
    }

    public function setNbReminders(?int $nbReminders): self
    {
        $this->propertyChanged('nbReminders', $this->nbReminders, $nbReminders);
        $this->nbReminders = $nbReminders;
        return $this;
    }

    public function getLastReminderDate(): ?DateTime
    {
        return $this->lastReminderDate;
    }

    public function setLastReminderDate(?DateTime $lastReminderDate): self
    {
        $this->propertyChanged('lastReminderDate', $this->lastReminderDate, $lastReminderDate);
        $this->lastReminderDate = $lastReminderDate;
        return $this;
    }
}
