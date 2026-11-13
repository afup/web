<?php

declare(strict_types=1);

namespace AppBundle\Association\Entity;

use AppBundle\Association\CompanyMembership\SubscriptionManagement;
use AppBundle\Association\Entity\Repository\CompanyMemberRepository;
use AppBundle\Association\Enum\CompanyMemberStatus;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompanyMemberRepository::class)]
#[ORM\Table(name: 'afup_personnes_morales')]
class CompanyMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'smallint', options: ['unsigned' => true])]
    public ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(name: 'prenom', length: 40)]
    public string $firstName = '';

    #[Assert\NotBlank]
    #[ORM\Column(name: 'nom', length: 40)]
    public string $lastName = '';

    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(length: 100)]
    public string $email = '';

    #[Assert\NotBlank(message: 'Raison sociale manquante')]
    #[ORM\Column(name: 'raison_sociale', length: 100)]
    public string $companyName = '';

    #[Assert\NotBlank]
    #[ORM\Column(length: 14)]
    public string $siret = '';

    #[Assert\NotBlank]
    #[ORM\Column(name: 'adresse', type: 'text')]
    public string $address = '';

    #[Assert\NotBlank]
    #[ORM\Column(name: 'code_postal', length: 10)]
    public string $zipCode = '';

    #[Assert\NotBlank]
    #[ORM\Column(name: 'ville', length: 50)]
    public string $city = '';

    #[Assert\NotBlank]
    #[ORM\Column(name: 'id_pays', length: 2, options: ['fixed' => true])]
    public string $country = 'FR';

    #[ORM\Column(name: 'telephone_fixe', length: 20, nullable: true)]
    public ?string $phone = null;

    #[ORM\Column(name: 'telephone_portable', length: 20, nullable: true)]
    public ?string $cellphone = null;

    #[ORM\Column(name: 'etat', type: 'smallint', enumType: CompanyMemberStatus::class)]
    public CompanyMemberStatus $status = CompanyMemberStatus::Active;

    #[ORM\Column(name: 'max_members', type: 'smallint', nullable: true, options: ['unsigned' => true])]
    public ?int $maxMembers = 0;

    // Colonne nullable en base (tinyint DEFAULT 0)
    #[ORM\Column(name: 'public_profile_enabled', nullable: true)]
    public ?bool $publicProfileEnabled = false;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $description = null;

    #[ORM\Column(name: 'logo_url', nullable: true)]
    public ?string $logoUrl = null;

    #[ORM\Column(name: 'website_url', nullable: true)]
    public ?string $websiteUrl = null;

    #[ORM\Column(name: 'contact_page_url', nullable: true)]
    public ?string $contactPageUrl = null;

    #[ORM\Column(name: 'careers_page_url', nullable: true)]
    public ?string $careersPageUrl = null;

    #[ORM\Column(name: 'twitter_handle', nullable: true)]
    public ?string $twitterHandle = null;

    #[ORM\Column(name: 'related_afup_offices', nullable: true)]
    public ?string $relatedAfupOffices = null;

    #[ORM\Column(name: 'membership_reason', nullable: true)]
    public ?string $membershipReason = null;

    /**
     * Non persisté : invitations saisies dans le formulaire d'adhésion entreprise.
     *
     * @var array<int, CompanyMemberInvitation|null>
     */
    public array $invitations = [];

    /**
     * Non persisté : date de fin de la dernière cotisation, renseignée par
     * CompanyMemberRepository (findById, loadAll, search, findDisplayableCompanies).
     */
    public ?\DateTimeImmutable $lastSubscription = null;

    /**
     * Invitations réellement saisies, en conservant leur position dans le formulaire
     * (l'index 0 correspond toujours au gestionnaire).
     *
     * @return array<int, CompanyMemberInvitation>
     */
    public function getValidInvitations(): array
    {
        return array_filter(
            $this->invitations,
            static fn($invitation): bool => $invitation instanceof CompanyMemberInvitation,
        );
    }

    public function hasLogoUrl(): bool
    {
        return null !== $this->logoUrl;
    }

    public function getCleanedTwitterHandle(): ?string
    {
        $twitter = (string) $this->twitterHandle;
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
     * @return list<string>
     */
    public function getFormattedRelatedAfupOffices(): array
    {
        if (null === $this->relatedAfupOffices) {
            return [];
        }

        return explode(',', $this->relatedAfupOffices);
    }

    /**
     * @param array<string> $relatedAfupOffices
     */
    public function setFormattedRelatedAfupOffices(array $relatedAfupOffices): void
    {
        if ($relatedAfupOffices === []) {
            $this->relatedAfupOffices = null;

            return;
        }

        sort($relatedAfupOffices);
        $this->relatedAfupOffices = implode(',', $relatedAfupOffices);
    }

    public function getSlug(): string
    {
        return new Slugify()->slugify($this->companyName);
    }

    public function hasUpToDateMembershipFee(?\DateTimeInterface $now = null): bool
    {
        if (!$now instanceof \DateTimeInterface) {
            $now = new \DateTime();
        }

        return $this->lastSubscription > $now;
    }

    public function getMembershipFee(int $default = SubscriptionManagement::AFUP_PERSONNE_MORALE_SEUIL): float
    {
        $max = max((int) $this->maxMembers, $default);

        return ceil($max / SubscriptionManagement::AFUP_PERSONNE_MORALE_SEUIL) * SubscriptionManagement::AFUP_COTISATION_PERSONNE_MORALE;
    }
}
