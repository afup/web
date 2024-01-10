<?php


namespace AppBundle\Association\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Cocur\Slugify\Slugify;
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
     * @var int
     */
    private $maxMembers = 0;

    /**
     * @var CompanyMemberInvitation[]
     */
    private $invitations;

    /**
     * @var bool
     */
    private $publicProfileEnabled = false;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var string|null
     */
    private $logoUrl;

    /**
     * @var string|null
     */
    private $websiteUrl;

    /**
     * @var string|null
     */
    private $contactPageUrl;

    /**
     * @var string|null
     */
    private $careersPageUrl;

    /**
     * @var string|null
     */
    private $twitterHandle;

    /**
     * @var string|null
     */
    private $relatedAfupOffices;

    /**
     * @var string|null
     */
    private $membershipReason;

    /**
     * @var \Datetime
     */
    private $lastSubscription;

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
        $this->propertyChanged('firstName', $this->firstName, $firstName);
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
        $this->propertyChanged('lastName', $this->lastName, $lastName);
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
        $this->propertyChanged('email', $this->email, $email);
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
        $this->propertyChanged('companyName', $this->companyName, $companyName);
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
        $this->propertyChanged('siret', $this->siret, $siret);
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
        $this->propertyChanged('address', $this->address, $address);
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
        $this->propertyChanged('zipCode', $this->zipCode, $zipCode);
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
        $this->propertyChanged('city', $this->city, $city);
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
        $this->propertyChanged('country', $this->country, $country);
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
        $this->propertyChanged('phone', $this->phone, $phone);
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
        $this->propertyChanged('status', $this->status, $status);
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
        $this->invitations = array_filter($invitations, static function ($invitation) {
            return $invitation instanceof CompanyMemberInvitation;
        });

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxMembers()
    {
        return $this->maxMembers;
    }

    /**
     * @param int $maxMembers
     * @return CompanyMember
     */
    public function setMaxMembers($maxMembers)
    {
        $this->propertyChanged('maxMembers', $this->maxMembers, $maxMembers);
        $this->maxMembers = $maxMembers;
        return $this;
    }

    /**
     * @return bool
     */
    public function getPublicProfileEnabled()
    {
        return (bool) $this->publicProfileEnabled;
    }

    /**
     * @param bool $publicProfileEnabled
     *
     * @return CompanyMember
     */
    public function setPublicProfileEnabled($publicProfileEnabled)
    {
        $this->propertyChanged('publicProfileEnabled', $this->publicProfileEnabled, $publicProfileEnabled);
        $this->publicProfileEnabled = $publicProfileEnabled;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return $this
     */
    public function setDescription($description = null)
    {
        $this->propertyChanged('description', $this->description, $description);
        $this->description = $description;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasLogoUrl()
    {
        return null !== $this->getLogoUrl();
    }

    /**
     * @return string|null
     */
    public function getLogoUrl()
    {
        return $this->logoUrl;
    }

    /**
     * @param string|null $logoUrl
     *
     * @return $this
     */
    public function setLogoUrl($logoUrl)
    {
        $this->propertyChanged('logoUrl', $this->logoUrl, $logoUrl);
        $this->logoUrl = $logoUrl;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getWebsiteUrl()
    {
        return $this->websiteUrl;
    }

    /**
     * @param string|null $websiteUrl
     *
     * @return CompanyMember
     */
    public function setWebsiteUrl($websiteUrl)
    {
        $this->propertyChanged('websiteUrl', $this->websiteUrl, $websiteUrl);
        $this->websiteUrl = $websiteUrl;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContactPageUrl()
    {
        return $this->contactPageUrl;
    }

    /**
     * @param string|null $contactPageUrl
     *
     * @return $this
     */
    public function setContactPageUrl($contactPageUrl)
    {
        $this->propertyChanged('contactPageUrl', $this->contactPageUrl, $contactPageUrl);
        $this->contactPageUrl = $contactPageUrl;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCareersPageUrl()
    {
        return $this->careersPageUrl;
    }

    /**
     * @param string|null $careersPageUrl
     *
     * @return $this
     */
    public function setCareersPageUrl($careersPageUrl)
    {
        $this->propertyChanged('careersPageUrl', $this->careersPageUrl, $careersPageUrl);
        $this->careersPageUrl = $careersPageUrl;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTwitterHandle()
    {
        return $this->twitterHandle;
    }

    /**
     * @return bool|string
     */
    public function getCleanedTwitterHandle()
    {
        $twitter = $this->getTwitterHandle();
        $twitter = trim($twitter, '@');
        $twitter = preg_replace('!^https?://twitter.com/!', '', $twitter);

        if (0 === strlen(trim($twitter))) {
            return null;
        }

        return $twitter;
    }

    /**
     * @param string|null $twitterHandle
     *
     * @return $this
     */
    public function setTwitterHandle($twitterHandle)
    {
        $this->propertyChanged('twitterHandle', $this->twitterHandle, $twitterHandle);
        $this->twitterHandle = $twitterHandle;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRelatedAfupOffices()
    {
        return $this->relatedAfupOffices;
    }

    /**
     * @param string|null $relatedAfupOffices
     *
     * @return $this
     */
    public function setRelatedAfupOffices($relatedAfupOffices)
    {
        $this->propertyChanged('relatedAfupOffices', $this->relatedAfupOffices, $relatedAfupOffices);
        $this->relatedAfupOffices = $relatedAfupOffices;

        return $this;
    }

    /**
     * @return array
     */
    public function getFormattedRelatedAfupOffices()
    {
        $relatedAfupOffices = $this->getRelatedAfupOffices();
        if (null === $relatedAfupOffices) {
            return [];
        }

        $relatedAfupOffices = explode(',', $relatedAfupOffices);

        return $relatedAfupOffices;
    }

    /**
     * @param array $relatedAfupOffices
     *
     * @return $this
     */
    public function setFormattedRelatedAfupOffices(array $relatedAfupOffices)
    {
        if (count($relatedAfupOffices)) {
            sort($relatedAfupOffices);

            $this->setRelatedAfupOffices(implode(',', $relatedAfupOffices));
        } else {
            $this->setRelatedAfupOffices(null);
        }

        return $this;
    }


    /**
     * @return string
     */
    public function getSlug()
    {
        $slugify = new Slugify();
        return $slugify->slugify($this->getCompanyName());
    }

    /**
     * @return \Datetime
     */
    public function getLastSubscription()
    {
        return $this->lastSubscription;
    }

    public function setLastSubscription($sub)
    {
        if ($sub !== null) {
            $this->lastSubscription = \DateTimeImmutable::createFromFormat('U', $sub);
        }
    }

    public function hasUpToDateMembershipFee(\DateTimeInterface $now = null)
    {
        if (null === $now) {
            $now = new \DateTime();
        }
        return $this->getLastSubscription() > $now;
    }

    /**
     * @return string|null
     */
    public function getMembershipReason()
    {
        return $this->membershipReason;
    }

    /**
     * @param string|null $membershipReason
     *
     * @return $this
     */
    public function setMembershipReason($membershipReason)
    {
        $this->propertyChanged('membershipReason', $this->membershipReason, $membershipReason);
        $this->membershipReason = $membershipReason;

        return $this;
    }
}
