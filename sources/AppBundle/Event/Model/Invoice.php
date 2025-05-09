<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use Afup\Site\Utils\Pays;
use AppBundle\Event\Validator\Constraints as AfupAssert;
use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Invoice implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var string
     */
    private $reference;

    private ?\DateTime $paymentDate = null;

    private ?\DateTime $invoiceDate = null;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var int
     */
    private $paymentType;

    /**
     * @var string
     */
    private $paymentInfos;

    /**
     * @var string
     */
    #[Assert\Email]
    private $email;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $lastname;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $firstname;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $address;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $zipcode;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $city;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $countryId = Pays::DEFAULT_ID;

    /**
     * @var string
     */
    private $authorization;

    /**
     * @var string
     */
    private $transaction;

    /**
     * @var int
     */
    private $status;

    /**
     * @var bool
     */
    private $invoice;

    /**
     * @var int
     */
    private $forumId;

    /**
     * @var Ticket[]
     * @AfupAssert\CorporateMember(groups={"corporate"})
     * @AfupAssert\TicketsCfpSubmitter()
     * @AfupAssert\EarlyBirdTicket()
     */
    #[Assert\Valid]
    private array $tickets = [];


    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     */
    public function setReference($reference): self
    {
        $this->propertyChanged('reference', $this->reference, $reference);
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPaymentDate(): ?\DateTime
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(\DateTime $paymentDate = null): self
    {
        $this->propertyChanged('paymentDate', $this->paymentDate, $paymentDate);
        $this->paymentDate = $paymentDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getInvoiceDate(): ?\DateTime
    {
        return $this->invoiceDate;
    }

    public function setInvoiceDate(\DateTime $invoiceDate = null): self
    {
        $this->propertyChanged('invoiceDate', $this->invoiceDate, $invoiceDate);
        $this->invoiceDate = $invoiceDate;
        return $this;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount): self
    {
        $this->propertyChanged('amount', $this->amount, $amount);
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return int
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    public function isFree(): bool
    {
        return $this->getAmount() == 0;
    }

    /**
     * @param int $paymentType
     */
    public function setPaymentType($paymentType): self
    {
        $this->propertyChanged('paymentType', $this->paymentType, $paymentType);
        $this->paymentType = $paymentType;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentInfos()
    {
        return $this->paymentInfos;
    }

    /**
     * @param string $paymentInfos
     */
    public function setPaymentInfos($paymentInfos): self
    {
        $this->propertyChanged('paymentInfos', $this->paymentInfos, $paymentInfos);
        $this->paymentInfos = $paymentInfos;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email): self
    {
        $this->propertyChanged('email', $this->email, $email);
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany($company): self
    {
        $this->propertyChanged('company', $this->company, $company);
        $this->company = $company;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname): self
    {
        $this->propertyChanged('lastname', $this->lastname, $lastname);
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname): self
    {
        $this->propertyChanged('firstname', $this->firstname, $firstname);
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address): self
    {
        $this->propertyChanged('address', $this->address, $address);
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * @param string $zipcode
     */
    public function setZipcode($zipcode): self
    {
        $this->propertyChanged('zipcode', $this->zipcode, $zipcode);
        $this->zipcode = $zipcode;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city): self
    {
        $this->propertyChanged('city', $this->city, $city);
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * @param string $countryId
     */
    public function setCountryId($countryId): self
    {
        $this->propertyChanged('countryId', $this->countryId, $countryId);
        $this->countryId = $countryId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorization()
    {
        return $this->authorization;
    }

    /**
     * @param string $authorization
     */
    public function setAuthorization($authorization): self
    {
        $this->propertyChanged('authorization', $this->authorization, $authorization);
        $this->authorization = $authorization;
        return $this;
    }

    /**
     * @return string
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param string $transaction
     */
    public function setTransaction($transaction): self
    {
        $this->propertyChanged('transaction', $this->transaction, $transaction);
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status): self
    {
        $this->propertyChanged('status', $this->status, $status);
        $this->status = $status;
        return $this;
    }

    /**
     * @return bool
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param bool $invoice
     */
    public function setInvoice($invoice): self
    {
        $this->propertyChanged('invoice', $this->invoice, $invoice);
        $this->invoice = $invoice;
        return $this;
    }

    /**
     * @return int
     */
    public function getForumId()
    {
        return $this->forumId;
    }

    /**
     * @param int $forumId
     */
    public function setForumId($forumId): self
    {
        $this->propertyChanged('forumId', $this->forumId, $forumId);
        $this->forumId = $forumId;
        return $this;
    }

    public function getTickets(): array
    {
        return $this->tickets;
    }

    public function setTickets(array $tickets): self
    {
        $this->tickets = $tickets;
        return $this;
    }

    public function addTicket(Ticket $ticket): self
    {
        $this->tickets[] = $ticket;
        return $this;
    }

    /**
     * @return string
     * @throw \RuntimeException
     */
    public function getLabel()
    {
        $label = $this->company ?? $this->lastname ?? null;
        if (!$label) {
            throw new \RuntimeException('Could not generate label');
        }
        return $label;
    }
}
