<?php

namespace AppBundle\Association\Form;

use AppBundle\Association\Model\CompanyMember;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyEditFormData
{
    public $companyId;
    /**
     * @Assert\NotBlank()
     */
    public $lastname;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=40)
     */
    public $firstname;
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(max=100)
     */
    public $email;
    /**
     * @Assert\NotBlank()
     */
    public $companyName;
    /**
     * @Assert\NotBlank()
     */
    public $address;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=4, max=10)
     */
    public $zipcode;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     */
    public $city;
    /** @var string */
    public $countryId = 'FR';
    /**
     * @Assert\Length(max=20)
     */
    public $phone;
    /**
     * @Assert\Length(max=20)
     */
    public $cellphone;
    /**
     * @var Assert\NotBlank()
     * @var Assert\Luhn()
     */
    public $siret;
    /**
     * @var Assert\NotBlank()
     * @Assert\Choice(choices={3, 6, 9, 12, 15, 18})
     */
    public $maxMembers = 3;
    /**
     * @Assert\Choice(choices={0, 1, -1}, strict=true)
     */
    public $status = CompanyMember::STATUS_ACTIVE;
}
