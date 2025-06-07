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

    public const STATUS_PENDING = -1;
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    private int $id;

    #[Assert\NotBlank]
    private string $firstName = '';

    #[Assert\NotBlank]
    private string $lastName = '';

    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email = '';

    #[Assert\NotBlank(message: 'Raison sociale manquante')]
    private string $companyName = '';

    #[Assert\NotBlank]
    private string $siret = '';

    #[Assert\NotBlank]
    private string $address = '';

    #[Assert\NotBlank]
    private string $zipCode = '';

    #[Assert\NotBlank]
    private string $city = '';

    #[Assert\NotBlank]
    private string $country = 'FR';

    private ?string $phone = null;

    private ?string $cellphone = null;

    /**
     * @var self::STATUS_*
     */
    private int $status = self::STATUS_ACTIVE;

    private ?int $maxMembers = 0;

    private ?bool $publicProfileEnabled = false;

    private ?string $description = null;

    private ?string $logoUrl = null;

    private ?string $websiteUrl = null;

    private ?string $contactPageUrl = null;

    private ?string $careersPageUrl = null;

    private ?string $twitterHandle = null;

    private ?string $relatedAfupOffices = null;

    private ?string $membershipReason = null;

    /**
     * @var CompanyMemberInvitation[]
     */
    private array $invitations = [];

    private ?\DateTimeImmutable $lastSubscription = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->propertyChanged('firstName', $this->firstName, $firstName);
        $this->firstName = (string) $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->propertyChanged('lastName', $this->lastName, $lastName);
        $this->lastName = (string) $lastName;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->propertyChanged('email', $this->email, $email);
        $this->email = (string) $email;
        return $this;
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): self
    {
        $this->propertyChanged('companyName', $this->companyName, $companyName);
        $this->companyName = (string) $companyName;
        return $this;
    }

    public function getSiret(): string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->propertyChanged('siret', $this->siret, $siret);
        $this->siret = (string) $siret;
        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->propertyChanged('address', $this->address, $address);
        $this->address = (string) $address;
        return $this;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): self
    {
        $this->propertyChanged('zipCode', $this->zipCode, $zipCode);
        $this->zipCode = (string) $zipCode;
        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->propertyChanged('city', $this->city, $city);
        $this->city = (string) $city;
        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->propertyChanged('country', $this->country, $country);
        $this->country = $country;
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

    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param self::STATUS_* $status
     */
    public function setStatus(int $status): self
    {
        $this->propertyChanged('status', $this->status, $status);
        $this->status = $status;
        return $this;
    }

    /**
     * @return CompanyMemberInvitation[]
     */
    public function getInvitations(): array
    {
        return $this->invitations ?? [];
    }

    /**
     * @param CompanyMemberInvitation[] $invitations
     */
    public function setInvitations(array $invitations): self
    {
        $this->invitations = array_filter($invitations, static fn($invitation): bool => $invitation instanceof CompanyMemberInvitation);

        return $this;
    }

    public function getMaxMembers(): int
    {
        return $this->maxMembers ?? 0;
    }

    public function setMaxMembers(int $maxMembers): self
    {
        $this->propertyChanged('maxMembers', $this->maxMembers, $maxMembers);
        $this->maxMembers = $maxMembers;
        return $this;
    }

    public function getPublicProfileEnabled(): bool
    {
        return (bool) $this->publicProfileEnabled;
    }

    public function setPublicProfileEnabled(bool $publicProfileEnabled): self
    {
        $this->propertyChanged('publicProfileEnabled', $this->publicProfileEnabled, $publicProfileEnabled);
        $this->publicProfileEnabled = $publicProfileEnabled;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->propertyChanged('description', $this->description, $description);
        $this->description = $description;

        return $this;
    }

    public function hasLogoUrl(): bool
    {
        return null !== $this->getLogoUrl();
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(?string $logoUrl): self
    {
        $this->propertyChanged('logoUrl', $this->logoUrl, $logoUrl);
        $this->logoUrl = $logoUrl;

        return $this;
    }

    public function getWebsiteUrl(): ?string
    {
        return $this->websiteUrl;
    }

    public function setWebsiteUrl(?string $websiteUrl): self
    {
        $this->propertyChanged('websiteUrl', $this->websiteUrl, $websiteUrl);
        $this->websiteUrl = $websiteUrl;

        return $this;
    }

    public function getContactPageUrl(): ?string
    {
        return $this->contactPageUrl;
    }

    public function setContactPageUrl(?string $contactPageUrl): self
    {
        $this->propertyChanged('contactPageUrl', $this->contactPageUrl, $contactPageUrl);
        $this->contactPageUrl = $contactPageUrl;

        return $this;
    }

    public function getCareersPageUrl(): ?string
    {
        return $this->careersPageUrl;
    }

    public function setCareersPageUrl(?string $careersPageUrl): self
    {
        $this->propertyChanged('careersPageUrl', $this->careersPageUrl, $careersPageUrl);
        $this->careersPageUrl = $careersPageUrl;

        return $this;
    }

    public function getTwitterHandle(): ?string
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

    public function setTwitterHandle(?string $twitterHandle): self
    {
        $this->propertyChanged('twitterHandle', $this->twitterHandle, $twitterHandle);
        $this->twitterHandle = $twitterHandle;

        return $this;
    }

    public function getRelatedAfupOffices(): ?string
    {
        return $this->relatedAfupOffices;
    }

    public function setRelatedAfupOffices(?string $relatedAfupOffices): self
    {
        $this->propertyChanged('relatedAfupOffices', $this->relatedAfupOffices, $relatedAfupOffices);
        $this->relatedAfupOffices = $relatedAfupOffices;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getFormattedRelatedAfupOffices(): array
    {
        $relatedAfupOffices = $this->getRelatedAfupOffices();
        if (null === $relatedAfupOffices) {
            return [];
        }

        return explode(',', $relatedAfupOffices);
    }

    /**
     * @param array<string> $relatedAfupOffices
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

    public function getSlug(): string
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

    public function getMembershipReason(): ?string
    {
        return $this->membershipReason;
    }

    public function setMembershipReason(?string $membershipReason): self
    {
        $this->propertyChanged('membershipReason', $this->membershipReason, $membershipReason);
        $this->membershipReason = $membershipReason;

        return $this;
    }

    public function getCellphone(): ?string
    {
        return $this->cellphone;
    }

    public function setCellphone(?string $cellphone): void
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
