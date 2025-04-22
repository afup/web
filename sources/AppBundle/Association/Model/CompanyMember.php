<?php

declare(strict_types=1);


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
     * @Assert\NotBlank(message="Raison sociale manquante")
     */
    private $companyName;

    /**
     * @var string
     * @Assert\NotBlank()
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

    /** @var string|null */
    private $cellphone;

    /**
     * @var int
     */
    private $status = self::STATUS_ACTIVE;

    /**
     * @var int
     */
    private $maxMembers = 0;

    /**
     * @var CompanyMemberInvitation[]
     */
    private ?array $invitations = null;

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

    private ?\DateTimeImmutable $lastSubscription = null;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): self
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
     */
    public function setFirstName($firstName): self
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
     */
    public function setLastName($lastName): self
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
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     */
    public function setCompanyName($companyName): self
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
     */
    public function setSiret($siret): self
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
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param string $zipCode
     */
    public function setZipCode($zipCode): self
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
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country): self
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
     */
    public function setPhone($phone): self
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
     */
    public function setStatus($status): self
    {
        $this->propertyChanged('status', $this->status, $status);
        $this->status = $status;
        return $this;
    }

    /**
     * @return CompanyMemberInvitation[]
     */
    public function getInvitations(): ?array
    {
        return $this->invitations;
    }

    /**
     * @param CompanyMemberInvitation[] $invitations
     */
    public function setInvitations($invitations): self
    {
        $this->invitations = array_filter($invitations, static fn ($invitation): bool => $invitation instanceof CompanyMemberInvitation);

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
     */
    public function setMaxMembers($maxMembers): self
    {
        $this->propertyChanged('maxMembers', $this->maxMembers, $maxMembers);
        $this->maxMembers = $maxMembers;
        return $this;
    }

    public function getPublicProfileEnabled(): bool
    {
        return (bool) $this->publicProfileEnabled;
    }

    /**
     * @param bool $publicProfileEnabled
     */
    public function setPublicProfileEnabled($publicProfileEnabled): self
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
    public function setDescription($description = null): self
    {
        $this->propertyChanged('description', $this->description, $description);
        $this->description = $description;

        return $this;
    }

    public function hasLogoUrl(): bool
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
    public function setLogoUrl($logoUrl): self
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
     */
    public function setWebsiteUrl($websiteUrl): self
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
    public function setContactPageUrl($contactPageUrl): self
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
    public function setCareersPageUrl($careersPageUrl): self
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

    public function getCleanedTwitterHandle(): ?string
    {
        $twitter = (string) $this->getTwitterHandle();
        $twitter = trim($twitter, '@');
        $twitter = preg_replace('!^https?://twitter.com/!', '', $twitter);

        if (!is_string($twitter)) {
            return null;
        }

        if (trim($twitter) === '') {
            return null;
        }

        return $twitter;
    }

    /**
     * @param string|null $twitterHandle
     *
     * @return $this
     */
    public function setTwitterHandle($twitterHandle): self
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
    public function setRelatedAfupOffices($relatedAfupOffices): self
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

        return explode(',', $relatedAfupOffices);
    }

    /**
     * @return $this
     */
    public function setFormattedRelatedAfupOffices(array $relatedAfupOffices): self
    {
        if ($relatedAfupOffices !== []) {
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

    public function getLastSubscription(): ?\DateTimeImmutable
    {
        return $this->lastSubscription;
    }

    public function setLastSubscription(?string $sub): void
    {
        if ($sub !== null) {
            $this->lastSubscription = new \DateTimeImmutable('@' . $sub);
        }
    }

    public function hasUpToDateMembershipFee(\DateTimeInterface $now = null): bool
    {
        if (!$now instanceof \DateTimeInterface) {
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
    public function setMembershipReason($membershipReason): self
    {
        $this->propertyChanged('membershipReason', $this->membershipReason, $membershipReason);
        $this->membershipReason = $membershipReason;

        return $this;
    }

    /** @return string|null */
    public function getCellphone()
    {
        return $this->cellphone;
    }

    /** @param string|null $cellphone */
    public function setCellphone($cellphone): void
    {
        $this->propertyChanged('cellphone', $this->cellphone, $cellphone);
        $this->cellphone = $cellphone;
    }

    public function getMembershipFee(int $default = AFUP_PERSONNE_MORALE_SEUIL): float
    {
        $max = max($this->getMaxMembers(), $default);

        return ceil($max / AFUP_PERSONNE_MORALE_SEUIL) * AFUP_COTISATION_PERSONNE_MORALE;
    }
}
