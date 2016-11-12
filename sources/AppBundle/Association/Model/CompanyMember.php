<?php


namespace AppBundle\Association\Model;


use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyMember implements NotifyPropertyInterface
{
    use NotifyProperty;

    const STATUS_PENDING = -1;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $companyName;

    /**
     * @var string
     */
    private $siret;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $address;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $zipCode;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $city;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $country = 'FR';

    /**
     * @var string
     */
    private $phone;

    /**
     * @var int
     */
    private $status = self::STATUS_PENDING;

    /**
     * @var CompanyMemberInvitation[]
     */
    private $invitations;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return CompanyMember
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return CompanyMember
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return CompanyMember
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
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
     * @return CompanyMember
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     * @return CompanyMember
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSiret()
    {
        return $this->siret;
    }

    /**
     * @param string $siret
     * @return CompanyMember
     */
    public function setSiret($siret)
    {
        $this->siret = $siret;
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
     * @return CompanyMember
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param string $zipCode
     * @return CompanyMember
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
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
     * @return CompanyMember
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return CompanyMember
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return CompanyMember
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
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
     * @return CompanyMember
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return CompanyMemberInvitation[]
     */
    public function getInvitations()
    {
        return $this->invitations;
    }

    /**
     * @param CompanyMemberInvitation[] $invitations
     * @return CompanyMember
     */
    public function setInvitations($invitations)
    {
        $this->invitations = $invitations;
        return $this;
    }
}
