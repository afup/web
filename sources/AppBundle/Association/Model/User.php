<?php

declare(strict_types=1);

namespace AppBundle\Association\Model;

use AppBundle\Antennes\AntenneRepository;
use AppBundle\Association\Genre;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\NotifiableInterface;
use AppBundle\Model\HasUniqueId;
use AppBundle\Validator\Constraints as AppAssert;
use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[AppAssert\UniqueEntity(fields: ['username'], repository: UserRepository::class)]
#[AppAssert\UniqueEntity(fields: ['email'], repository: UserRepository::class)]
class User implements NotifyPropertyInterface, NotifiableInterface, UserInterface, PasswordAuthenticatedUserInterface, HasUniqueId
{
    use NotifyProperty;

    public const int LEVEL_NONE = 0;
    public const int LEVEL_READER = 1;
    public const int LEVEL_MANAGER = 2;

    /**
     * Modules du back-office, dans l'ordre d'affichage. Le niveau d'accès à un module
     * est porté par les rôles ROLE_<MODULE>_READER (lecteur) et ROLE_<MODULE>_WRITER (gestionnaire).
     */
    public const array MODULES = [
        'membres' => 'Membres',
        'veille' => 'Veille',
        'site' => 'Site',
        'apero' => 'Super-apéro',
        'event' => 'Évènements',
        'compta' => 'Trésorerie',
        'ag' => 'Assemblée générale',
        'planete' => 'Planète',
        'antennes' => 'Antennes',
    ];

    public const int STATUS_PENDING = -1;
    public const int STATUS_ACTIVE = 1;
    public const int STATUS_INACTIVE = 0;

    public const int SLACK_INVITE_STATUS_NONE = 0;
    public const int SLACK_INVITE_STATUS_REQUESTED = 1;
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $companyId = 0;

    /**
     * @var string
     */
    private $username;

    /**
     * @var ?string
     */
    private $password;

    /**
     * @var array<string>
     */
    private $roles = [];

    private ?Genre $genre = null;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string|null
     */
    private $alternateEmail;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $zipCode;

    /**
     * @var string
     */
    private $city;

    private string $country = 'FR';

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $mobilephone;

    /**
     * @var int
     */
    private $status = 0;

    private ?\DateTime $reminderDate = null;

    /**
     * @var string
     */
    private $hash;

    private ?\DateTimeImmutable $lastSubscription = null;

    private ?CompanyMember $company = null;

    private ?string $nearestOffice = null;

    /**
     * @var int
     */
    private $slackInviteStatus = self::SLACK_INVITE_STATUS_NONE;

    /**
     * @var bool
     */
    private $needsUpToDateMembership = false;

    private ?\DateTimeImmutable $lastLogin = null;

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
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getMobilephone()
    {
        return $this->mobilephone;
    }

    /**
     * @param string $mobilephone
     */
    public function setMobilephone($mobilephone): self
    {
        $this->propertyChanged('mobilephone', $this->mobilephone, $mobilephone);
        $this->mobilephone = $mobilephone;
        return $this;
    }

    public function getNearestOffice(): ?string
    {
        return $this->nearestOffice;
    }

    /**
     * @param string $nearestOffice
     */
    public function setNearestOffice($nearestOffice): self
    {
        $this->propertyChanged('nearestOffice', $this->nearestOffice, $nearestOffice);
        $this->nearestOffice = $nearestOffice;
        return $this;
    }

    public function getNearestOfficeLabel(AntenneRepository $antenneRepository)
    {
        $code = $this->getNearestOffice();

        // FIXME corriger ça dans le formulaire
        if (null === $code || '-Aucune-' === $code || trim($code) === '') {
            return  null;
        }

        return $antenneRepository->findByCode($code)->label;
    }

    /**
     * @return int
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @param int $companyId
     */
    public function setCompanyId($companyId): self
    {
        $this->propertyChanged('companyId', $this->companyId, $companyId);
        $this->companyId = $companyId;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     *
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username): self
    {
        $this->propertyChanged('username', $this->username, $username);
        $this->username = $username;
        return $this;
    }

    /**
     * This method can be removed in Symfony 6.0 - is not needed for apps that do not check user passwords.
     *
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param ?string $password
     */
    public function setPassword($password): self
    {
        $this->propertyChanged('password', $this->password, $password);
        $this->password = $password;
        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): void
    {
        $this->propertyChanged('genre', $this->genre, $genre);
        $this->genre = $genre;
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
     * @return \DateTime
     */
    public function getReminderDate(): ?\DateTime
    {
        return $this->reminderDate;
    }

    public function setReminderDate(?\DateTime $reminderDate = null): self
    {
        $this->propertyChanged('reminderDate', $this->reminderDate, $reminderDate);
        $this->reminderDate = $reminderDate;
        return $this;
    }

    public function getLastLogin(): ?\DateTimeImmutable
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeImmutable $lastLogin): self
    {
        $this->propertyChanged('lastLogin', $this->lastLogin, $lastLogin);
        $this->lastLogin = $lastLogin;
        return $this;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash): self
    {
        $this->propertyChanged('hash', $this->hash, $hash);
        $this->hash = $hash;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getLastSubscription(): ?\DateTimeImmutable
    {
        return $this->lastSubscription;
    }

    public function setLastSubscription(?string $sub): void
    {
        if ($sub !== null) {
            $this->lastSubscription = \DateTimeImmutable::createFromFormat('U', $sub);
        }
    }

    public function hasUpToDateMembershipFee(?\DateTimeInterface $now = null): bool
    {
        if (!$now instanceof \DateTimeInterface) {
            $now = new \DateTime();
        }
        return $this->getLastSubscription() > $now;
    }

    public function getDaysBeforeMembershipExpiration(?\DateTimeInterface $now = null)
    {
        if (!$now instanceof \DateTimeInterface) {
            $now = new \DateTime();
        }

        return $this->lastSubscription?->diff($now)->days;
    }

    /**
     * @return CompanyMember
     */
    public function getCompany(): ?CompanyMember
    {
        return $this->company;
    }

    public function setCompany(CompanyMember $company): self
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return int
     */
    public function getSlackInviteStatus()
    {
        return $this->slackInviteStatus;
    }

    /**
     * @param int $slackInviteStatus
     */
    public function setSlackInviteStatus($slackInviteStatus): self
    {
        $this->propertyChanged('slackInviteStatus', $this->slackInviteStatus, $slackInviteStatus);
        $this->slackInviteStatus = $slackInviteStatus;

        return $this;
    }

    public function canRequestSlackInvite(): bool
    {
        return false === $this->hasRole('ROLE_MEMBER_EXPIRED') && $this->getSlackInviteStatus() === self::SLACK_INVITE_STATUS_NONE;
    }

    public function slackInviteRequested(): bool
    {
        return $this->getSlackInviteStatus() === self::SLACK_INVITE_STATUS_REQUESTED;
    }

    public function canAccessAdmin(): bool
    {
        foreach ($this->getRoles() as $role) {
            if (
                $role === 'ROLE_ADMIN'
                || $role === 'ROLE_SUPER_ADMIN'
                || str_ends_with($role, '_READER')
                || str_ends_with($role, '_WRITER')
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Accès à l'admin (checkbox "Accès à l'admin") : marqueur ROLE_ADMIN de la console,
     * sans aucun droit fonctionnel — ceux-ci viennent des rôles des modules.
     */
    public function isAdminAccess(): bool
    {
        return in_array('ROLE_ADMIN', $this->roles, true);
    }

    public function setAdminAccess(bool $adminAccess): self
    {
        return $this->setRoleGranted('ROLE_ADMIN', $adminAccess);
    }

    /**
     * Niveau d'accès au module $module (clé de User::MODULES) :
     * LEVEL_NONE, LEVEL_READER (ROLE_<MODULE>_READER) ou LEVEL_MANAGER (ROLE_<MODULE>_WRITER).
     */
    public function getModuleLevel(string $module): int
    {
        $prefix = 'ROLE_' . strtoupper($module);

        if (in_array($prefix . '_WRITER', $this->roles, true)) {
            return self::LEVEL_MANAGER;
        }

        if (in_array($prefix . '_READER', $this->roles, true)) {
            return self::LEVEL_READER;
        }

        return self::LEVEL_NONE;
    }

    public function setModuleLevel(string $module, int $level): self
    {
        if (!array_key_exists($module, self::MODULES)) {
            return $this;
        }

        $prefix = 'ROLE_' . strtoupper($module);

        return $this
            ->setRoleGranted($prefix . '_READER', $level === self::LEVEL_READER)
            ->setRoleGranted($prefix . '_WRITER', $level === self::LEVEL_MANAGER);
    }

    public function isMemberForCompany(): bool
    {
        return ($this->companyId !== null && $this->companyId > 0);
    }

    public function getRoles(): array
    {
        $defaultRoles = ['ROLE_USER'];
        if ($this->lastSubscription < new \DateTime()) {
            $defaultRoles = ['ROLE_MEMBER_EXPIRED'];
        }

        // On enlève le rôle ROLE_MEMBER_EXPIRED vu qu'il est défini en fonction de la cotisation dans les defaultRoles
        $userRoles = array_diff($this->roles, ['ROLE_MEMBER_EXPIRED']);

        return array_unique(array_merge($userRoles, $defaultRoles));
    }

    /**
     * @param $role
     */
    public function hasRole($role): bool
    {
        $roles = $this->getRoles();
        return in_array($role, $roles);
    }

    /**
     * @param array<string>|null $roles
     */
    public function setRoles(?array $roles = null): self
    {
        if ($roles === null) {
            $roles = [];
        }
        $this->propertyChanged('roles', $this->roles, $roles);
        $this->roles = $roles;
        return $this;
    }

    /**
     * @param string $role
     */
    public function addRole($role): self
    {
        $roles = $this->roles;
        $this->roles[] = $role;
        $this->roles = array_unique($this->roles);

        $this->propertyChanged('roles', $roles, $this->roles);

        return $this;
    }

    /**
     * @param string $role
     */
    public function removeRole($role): self
    {
        if ($this->hasRole($role)) {
            $roleNum = array_search($role, $this->roles);
            $oldRoles = $this->roles;
            unset($this->roles[$roleNum]);
            $this->propertyChanged('roles', $oldRoles, $this->roles);
        }

        return $this;
    }

    /** Ajoute ou retire $role des rôles enregistrés de la personne. */
    public function setRoleGranted(string $role, bool $granted): self
    {
        $roles = array_values(array_diff($this->roles, [$role]));
        if ($granted) {
            $roles[] = $role;
        }

        if ($roles !== $this->roles) {
            $this->setRoles($roles);
        }

        return $this;
    }

    public function eraseCredentials(): void {}

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
        ];
    }

    /**
     * @param array{id: int, username: string, password: ?string} $serialized
     */
    public function __unserialize($serialized): void
    {
        $this->id = $serialized['id'];
        $this->username = $serialized['username'];
        $this->password = $serialized['password'];
    }

    public function getAlternateEmail()
    {
        return $this->alternateEmail;
    }

    public function setAlternateEmail($alternateEmail): void
    {
        $this->propertyChanged('alternateEmail', $this->alternateEmail, $alternateEmail);
        $this->alternateEmail = $alternateEmail;
    }

    public function getNeedsUpToDateMembership()
    {
        return $this->needsUpToDateMembership;
    }

    public function setNeedsUpToDateMembership($needsUpToDateMembership): void
    {
        $this->propertyChanged('needsUpToDateMembership', $this->needsUpToDateMembership, $needsUpToDateMembership);
        $this->needsUpToDateMembership = $needsUpToDateMembership;
    }

    public function uniqueId(): ?string
    {
        if ($this->id === null) {
            return null;
        }

        return (string) $this->id;
    }
}
