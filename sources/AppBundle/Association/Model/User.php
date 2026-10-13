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

    public const int LEVEL_MEMBER = 0;
    public const int LEVEL_READER = 1;
    public const int LEVEL_ADMIN = 2;

    public const string LEVEL_LABEL_NONE = '--';
    public const string LEVEL_LABEL_READER = 'Lecture seule';
    public const string LEVEL_LABEL_ADMIN = 'Gestionnaire';

    /**
     * Modules du menu admin, dans l'ordre des index de la chaîne $levelModules.
     * Index 0 à 4 = héritage historique (apéro, annuaire, site, forum/évènement, antenne).
     * Index 5 à 8 = modules ajoutés par la matrice READ/WRITE (veille, trésorerie, AG, planète).
     */
    public const array MODULES = ['apero', 'membres', 'site', 'event', 'antennes', 'veille', 'compta', 'ag', 'planete'];

    private const array MODULE_ROLE_SUFFIXES = ['APERO', 'MEMBRES', 'SITE', 'EVENT', 'ANTENNES', 'VEILLE', 'COMPTA', 'AG', 'PLANETE'];

    /** Rôles legacy encore tolérés dans la colonne roles en base, tous recalculés à partir des niveau-modules. */
    private const array LEGACY_COMPUTED_ROLES = ['ROLE_APERO', 'ROLE_ANNUAIRE', 'ROLE_SITE', 'ROLE_FORUM', 'ROLE_ANTENNE'];

    /** Tous les rôles fonctionnels de la matrice READ/WRITE, recalculés à partir des niveau-modules. */
    private const array COMPUTED_MATRIX_ROLES = [
        'ROLE_APERO_READER', 'ROLE_APERO_WRITER',
        'ROLE_MEMBRES_READER', 'ROLE_MEMBRES_WRITER',
        'ROLE_SITE_READER', 'ROLE_SITE_WRITER',
        'ROLE_EVENT_READER', 'ROLE_EVENT_WRITER',
        'ROLE_ANTENNES_READER', 'ROLE_ANTENNES_WRITER',
        'ROLE_VEILLE_READER', 'ROLE_VEILLE_WRITER',
        'ROLE_COMPTA_READER', 'ROLE_COMPTA_WRITER',
        'ROLE_AG_READER', 'ROLE_AG_WRITER',
        'ROLE_PLANETE_READER', 'ROLE_PLANETE_WRITER',
    ];

    /** Chaîne de niveaux : 9 digits (index 0=apéro, 1=annuaire/membres, 2=site, 3=forum/évènement, 4=antenne, 5=veille, 6=compta, 7=ag, 8=planète). */
    public const string DEFAULT_LEVEL_MODULES = '000000000';
    private const int MODULE_LENGTH = 9;

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
     * @var int
     */
    private $level = self::LEVEL_MEMBER;

    /**
     * @var string
     */
    private $levelModules = '000000000';

    /**
     * @var array
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

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel($level): self
    {
        $this->propertyChanged('level', $this->level, $level);
        $this->level = $level;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelModules()
    {
        return $this->levelModules;
    }

    /**
     * @param string $levelModules
     */
    public function setLevelModules($levelModules): self
    {
        $levelModules = str_pad((string) $levelModules, self::MODULE_LENGTH, (string) self::LEVEL_MEMBER, STR_PAD_RIGHT);
        $this->propertyChanged('levelModules', $this->levelModules, $levelModules);
        $this->levelModules = $levelModules;
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
                || in_array($role, self::LEGACY_COMPUTED_ROLES, true)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Accès à l'admin (checkbox "Accès à l'admin") : marqueur ROLE_ADMIN de la console,
     * sans aucun droit fonctionnel — ceux-ci viennent des niveau-modules.
     */
    public function isAdminAccess(): bool
    {
        return $this->level === self::LEVEL_ADMIN;
    }

    public function setAdminAccess(bool $adminAccess): self
    {
        return $this->setLevel($adminAccess ? self::LEVEL_ADMIN : self::LEVEL_MEMBER);
    }

    /** Chaîne des niveaux modules normalisée sur 9 caractères ('000000000'). */
    private function paddedLevelModules(): string
    {
        return str_pad($this->levelModules, self::MODULE_LENGTH, (string) self::LEVEL_MEMBER, STR_PAD_RIGHT);
    }

    /**
     * Niveau du module admin ($module parmi User::MODULES) :
     * LEVEL_NONE ('--'), LEVEL_READER ('Lecture seule') ou LEVEL_ADMIN ('Gestionnaire').
     */
    public function getModuleLevel(string $module): int
    {
        $index = array_search($module, self::MODULES, true);
        if ($index === false) {
            return self::LEVEL_MEMBER;
        }

        return min((int) $this->paddedLevelModules()[$index], self::LEVEL_ADMIN);
    }

    public function setModuleLevel(string $module, int $level): self
    {
        $index = array_search($module, self::MODULES, true);
        if ($index === false) {
            return $this;
        }

        $levels = str_split($this->paddedLevelModules());
        $levels[$index] = (string) $level;
        $this->setLevelModules(implode('', $levels));

        return $this;
    }

    /** Niveau historique d'annuaire des prestataires / membres (index 1). */
    public function getDirectoryLevel(): int
    {
        return $this->getModuleLevel('membres');
    }

    public function setDirectoryLevel(int $level): self
    {
        return $this->setModuleLevel('membres', $level);
    }

    /** Niveau historique de site (index 2). */
    public function getWebsiteLevel(): int
    {
        return $this->getModuleLevel('site');
    }

    public function setWebsiteLevel(int $level): void
    {
        $this->setModuleLevel('site', $level);
    }

    /** Niveau historique d'évènement / forum (index 3). */
    public function getEventLevel(): int
    {
        return $this->getModuleLevel('event');
    }

    public function setEventLevel(int $level): void
    {
        $this->setModuleLevel('event', $level);
    }

    /** Niveau historique d'antenne (index 4). */
    public function getOfficeLevel(): int
    {
        return $this->getModuleLevel('antennes');
    }

    public function setOfficeLevel(int $level): void
    {
        $this->setModuleLevel('antennes', $level);
    }

    public function getAperoLevel(): int
    {
        return $this->getModuleLevel('apero');
    }

    public function setAperoLevel(int $level): self
    {
        return $this->setModuleLevel('apero', $level);
    }

    public function getMembresLevel(): int
    {
        return $this->getModuleLevel('membres');
    }

    public function setMembresLevel(int $level): self
    {
        return $this->setModuleLevel('membres', $level);
    }

    public function getSiteLevel(): int
    {
        return $this->getModuleLevel('site');
    }

    public function setSiteLevel(int $level): self
    {
        return $this->setModuleLevel('site', $level);
    }

    public function getAntennesLevel(): int
    {
        return $this->getModuleLevel('antennes');
    }

    public function setAntennesLevel(int $level): self
    {
        return $this->setModuleLevel('antennes', $level);
    }

    public function getVeilleLevel(): int
    {
        return $this->getModuleLevel('veille');
    }

    public function setVeilleLevel(int $level): self
    {
        return $this->setModuleLevel('veille', $level);
    }

    public function getComptaLevel(): int
    {
        return $this->getModuleLevel('compta');
    }

    public function setComptaLevel(int $level): self
    {
        return $this->setModuleLevel('compta', $level);
    }

    public function getAgLevel(): int
    {
        return $this->getModuleLevel('ag');
    }

    public function setAgLevel(int $level): self
    {
        return $this->setModuleLevel('ag', $level);
    }

    public function getPlaneteLevel(): int
    {
        return $this->getModuleLevel('planete');
    }

    public function setPlaneteLevel(int $level): self
    {
        return $this->setModuleLevel('planete', $level);
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
        if ($this->isAdminAccess()) {
            // Un admin de base console seulement : ROLE_ADMIN est vide (marqueur d'accès console).
            // Les droits fonctionnels viennent des niveau-modules ci-dessous.
            // ROLE_SUPER_ADMIN est désormais réservé et ne doit plus être attribué automatiquement.
            $defaultRoles[] = 'ROLE_ADMIN';
        }
        foreach (str_split($this->paddedLevelModules()) as $index => $level) {
            if (! isset(self::MODULE_ROLE_SUFFIXES[$index]) || $level === (string) self::LEVEL_MEMBER) {
                continue;
            }
            $suffix = self::MODULE_ROLE_SUFFIXES[$index];
            $defaultRoles[] = $level === (string) self::LEVEL_READER
                ? 'ROLE_' . $suffix . '_READER'
                : 'ROLE_' . $suffix . '_WRITER';
        }

        // On enlève le rôle ROLE_MEMBER_EXPIRED vu qu'il est défini en fonction de la cotisation dans les defaultRoles ;
        // on fait de même avec les rôles de la matrice READ/WRITE et les rôles legacy
        // définis en fonction des champs niveau-modules, car ils peuvent se retrouver en base
        // dans le tableau de rôles, et on ne veut pas que si on modifie les niveau-modules pour les enlever,
        // ils restent via les roles.
        $computedRoles = array_merge(self::LEGACY_COMPUTED_ROLES, self::COMPUTED_MATRIX_ROLES);
        $userRoles = array_diff($this->roles, ['ROLE_MEMBER_EXPIRED', 'ROLE_ADMIN'], $computedRoles);

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
