<?php

namespace AppBundle\Payment;

use AppBundle\Event\Model\Invoice;

class PayboxBilling
{
    private $firstName;
    private $lastName;
    private $address1;
    private $zipCode;
    private $city;
    private $countryCode;

    public function __construct($firstName, $lastName, $address1, $zipCode, $city, $countryCode)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->address1 = $address1;
        $this->zipCode = $zipCode;
        $this->city = $city;
        $this->countryCode = $countryCode;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return mixed
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @return mixed
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return mixed
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    public static function createFromInvoice(Invoice $invoice)
    {
        return new PayboxBilling($invoice->getFirstname(), $invoice->getLastname(), $invoice->getAddress(), $invoice->getZipcode(), $invoice->getCity(), $invoice->getCountryIso3166Numeric());
    }
}
