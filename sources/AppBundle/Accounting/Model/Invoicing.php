<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Model;

use AppBundle\Accounting\InvoicingCurrency;
use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use DateTime;

class Invoicing implements NotifyPropertyInterface
{
    use NotifyProperty;

    private ?int $id = null;
    private ?DateTime $quotationDate = null;
    private ?string $quotationNumber = null;
    private ?DateTime $invoiceDate = null;
    private ?string $invoiceNumber = null;
    private ?string $company = null;
    private ?string $service = null;
    private ?string $address = null;
    private ?string $zipcode = null;
    private ?string $city = null;
    private ?string $countryId = null;
    private ?string $email = null;
    private ?string $tvaIntra = null;
    private ?string $observation = null;
    private ?string $refClt1 = null;
    private ?string $refClt2 = null;
    private ?string $refClt3 = null;
    private ?string $lastname = null;
    private ?string $firstname = null;
    private ?string $phone = null;
    private int $paymentStatus = 0;
    private ?DateTime $paymentDate = null;
    private ?InvoicingCurrency $currency = null;

    private ?float $price = null;

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

    public function getQuotationDate(): ?DateTime
    {
        return $this->quotationDate;
    }

    public function setQuotationDate(?DateTime $quotationDate): self
    {
        $this->propertyChanged('quotationDate', $this->quotationDate, $quotationDate);
        $this->quotationDate = $quotationDate;

        return $this;
    }

    public function getQuotationNumber(): ?string
    {
        return $this->quotationNumber;
    }

    public function setQuotationNumber(?string $quotationNumber): self
    {
        $this->propertyChanged('quotationNumber', $this->quotationNumber, $quotationNumber);
        $this->quotationNumber = $quotationNumber;

        return $this;
    }

    public function getInvoiceDate(): ?DateTime
    {
        return $this->invoiceDate;
    }

    public function setInvoiceDate(?DateTime $invoiceDate): self
    {
        $this->propertyChanged('invoiceDate', $this->invoiceDate, $invoiceDate);
        $this->invoiceDate = $invoiceDate;

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

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->propertyChanged('company', $this->company, $company);
        $this->company = $company;

        return $this;
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(?string $service): self
    {
        $this->propertyChanged('service', $this->service, $service);
        $this->service = $service;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->propertyChanged('address', $this->address, $address);
        $this->address = $address;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): self
    {
        $this->propertyChanged('zipcode', $this->zipcode, $zipcode);
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->propertyChanged('city', $this->city, $city);
        $this->city = $city;

        return $this;
    }

    public function getCountryId(): ?string
    {
        return $this->countryId;
    }

    public function setCountryId(?string $countryId): self
    {
        $this->propertyChanged('countryId', $this->countryId, $countryId);
        $this->countryId = $countryId;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->propertyChanged('email', $this->email, $email);
        $this->email = $email;

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

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): self
    {
        $this->propertyChanged('observation', $this->observation, $observation);
        $this->observation = $observation;

        return $this;
    }

    public function getRefClt1(): ?string
    {
        return $this->refClt1;
    }

    public function setRefClt1(?string $refClt1): self
    {
        $this->propertyChanged('refClt1', $this->refClt1, $refClt1);
        $this->refClt1 = $refClt1;

        return $this;
    }

    public function getRefClt2(): ?string
    {
        return $this->refClt2;
    }

    public function setRefClt2(?string $refClt2): self
    {
        $this->propertyChanged('refClt2', $this->refClt2, $refClt2);
        $this->refClt2 = $refClt2;

        return $this;
    }

    public function getRefClt3(): ?string
    {
        return $this->refClt3;
    }

    public function setRefClt3(?string $refClt3): self
    {
        $this->propertyChanged('refClt3', $this->refClt3, $refClt3);
        $this->refClt3 = $refClt3;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->propertyChanged('lastname', $this->lastname, $lastname);
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->propertyChanged('firstname', $this->firstname, $firstname);
        $this->firstname = $firstname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->propertyChanged('phone', $this->phone, $phone);
        $this->phone = $phone;

        return $this;
    }

    public function getPaymentStatus(): int
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(int $paymentStatus): self
    {
        $this->propertyChanged('paymentStatus', $this->paymentStatus, $paymentStatus);
        $this->paymentStatus = $paymentStatus;

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

    public function getCurrency(): ?InvoicingCurrency
    {
        return $this->currency;
    }

    public function setCurrency(?InvoicingCurrency $currency): self
    {
        $this->propertyChanged('currency', $this->currency, $currency);
        $this->currency = $currency;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
