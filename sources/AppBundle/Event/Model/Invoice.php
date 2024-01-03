<?php

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

    /**
     * @var \DateTime
     */
    private $paymentDate;

    /**
     * @var \DateTime
     */
    private $invoiceDate;

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
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $address;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $zipcode;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $city;

    /**
     * @var string
     * @Assert\NotBlank()
     */
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
     * @Assert\Valid()
     * @AfupAssert\CorporateMember(groups={"corporate"})
     * @AfupAssert\TicketsCfpSubmitter()
     * @AfupAssert\EarlyBirdTicket()
     */
    private $tickets = [];


    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     * @return Invoice
     */
    public function setReference($reference)
    {
        $this->propertyChanged('reference', $this->reference, $reference);
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * @param \DateTime $paymentDate
     * @return Invoice
     */
    public function setPaymentDate(\DateTime $paymentDate = null)
    {
        $this->propertyChanged('paymentDate', $this->paymentDate, $paymentDate);
        $this->paymentDate = $paymentDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getInvoiceDate()
    {
        return $this->invoiceDate;
    }

    /**
     * @param \DateTime $invoiceDate
     * @return Invoice
     */
    public function setInvoiceDate(\DateTime $invoiceDate = null)
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
     * @return Invoice
     */
    public function setAmount($amount)
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

    public function isFree()
    {
        return $this->getAmount() == 0;
    }

    /**
     * @param int $paymentType
     * @return Invoice
     */
    public function setPaymentType($paymentType)
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
     * @return Invoice
     */
    public function setPaymentInfos($paymentInfos)
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
     * @return Invoice
     */
    public function setEmail($email)
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
     * @return Invoice
     */
    public function setCompany($company)
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
     * @return Invoice
     */
    public function setLastname($lastname)
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
     * @return Invoice
     */
    public function setFirstname($firstname)
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
     * @return Invoice
     */
    public function setAddress($address)
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
     * @return Invoice
     */
    public function setZipcode($zipcode)
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
     * @return Invoice
     */
    public function setCity($city)
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

    public function getCountryIso3166Numeric()
    {
        $country = $this->getCountryId();

        try {
            $data = (new \League\ISO3166\ISO3166)->alpha2($country);

            return $data['numeric'];
        } catch (\Exception $exception) {

            return null;
        }
    }

    /**
     * @param string $countryId
     * @return Invoice
     */
    public function setCountryId($countryId)
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
     * @return Invoice
     */
    public function setAuthorization($authorization)
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
     * @return Invoice
     */
    public function setTransaction($transaction)
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
     * @return Invoice
     */
    public function setStatus($status)
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
     * @return Invoice
     */
    public function setInvoice($invoice)
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
     * @return Invoice
     */
    public function setForumId($forumId)
    {
        $this->propertyChanged('forumId', $this->forumId, $forumId);
        $this->forumId = $forumId;
        return $this;
    }

    /**
     * @return array
     */
    public function getTickets()
    {
        return $this->tickets;
    }

    /**
     * @param array $tickets
     * @return Invoice
     */
    public function setTickets(array $tickets)
    {
        $this->tickets = $tickets;
        return $this;
    }

    /**
     * @param Ticket $ticket
     * @return Invoice
     */
    public function addTicket(Ticket $ticket)
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
        if ($this->company !== null) {
            return $this->company;
        }
        if ($this->lastname !== null) {
            return $this->lastname;
        }
        throw new \RuntimeException('Could not generate label');
    }
}
