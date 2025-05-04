<?php

declare(strict_types=1);

namespace AppBundle\Association\Model;

use AppBundle\Antennes\AntennesCollection;
use AppBundle\Association\NotifiableInterface;
use AppBundle\Validator\Constraints as AppAssert;
use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @AppAssert\UniqueEntity(fields={"username"}, repository="\AppBundle\Association\Model\Repository\UserRepository")
 * @AppAssert\UniqueEntity(fields={"email"}, repository="\AppBundle\Association\Model\Repository\UserRepository")
 */
class User implements NotifyPropertyInterface, NotifiableInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    use NotifyProperty;

    const LEVEL_MEMBER = 0;
    const LEVEL_WRITER = 1;
    const LEVEL_ADMIN = 2;

    const STATUS_PENDING = -1;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const SLACK_INVITE_STATUS_NONE = 0;
    const SLACK_INVITE_STATUS_REQUESTED = 1;

    const CIVILITE_M = 0;
    const CIVILITE_MME = 1;

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
     * @var string
     */
    private $password;

    /**
     * @var int
     */
    private $level = self::LEVEL_MEMBER;

    /**
     * @var string
     */
    private $levelModules = '00000';

    /**
     * @var array
     */
    private $roles = [];

    /**
     * @var string
     */
    private $civility;

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

    /**
     * @var string
     */
    private $country = 'FR';

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

    /**
     * @var \DateTimeImmutable
     */
    private $lastSubscription;

    private ?CompanyMember $company = null;

    /**
     * @var string
     */
    private $nearestOffice;

    /**
     * @var int
     */
    private $slackInviteStatus = self::SLACK_INVITE_STATUS_NONE;

    /**
     * @var bool
     */
    private $needsUpToDateMembership = false;

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

    /**
     * @return string
     */
    public function getNearestOffice()
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

    public function getNearestOfficeLabel()
    {
        $code = $this->getNearestOffice();

        // FIXME corriger ça dans le formulaire
        if (null === $code || '-Aucune-' === $code || trim($code) === '') {
            return  null;
        }

        return (new AntennesCollection())->findByCode($code)->label;
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
     * @param string $password
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
        $this->propertyChanged('levelModules', $this->levelModules, $levelModules);
        $this->levelModules = $levelModules;
        return $this;
    }

    /**
     * @return string
     */
    public function getCivility()
    {
        return $this->civility;
    }

    /**
     * @param string $civility
     */
    public function setCivility($civility): self
    {
        $this->propertyChanged('civility', $this->civility, $civility);
        $this->civility = $civility;
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
     * @return \DateTime
     */
    public function getReminderDate(): ?\DateTime
    {
        return $this->reminderDate;
    }

    public function setReminderDate(\DateTime $reminderDate = null): self
    {
        $this->propertyChanged('reminderDate', $this->reminderDate, $reminderDate);
        $this->reminderDate = $reminderDate;
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

    /**
     * @return \DateTimeImmutable
     */
    public function getLastSubscription()
    {
        return $this->lastSubscription;
    }

    public function setLastSubscription(?string $sub): void
    {
        if ($sub !== null) {
            $this->lastSubscription = \DateTimeImmutable::createFromFormat('U', $sub);
        }
    }

    public function hasUpToDateMembershipFee(\DateTimeInterface $now = null): bool
    {
        if (!$now instanceof \DateTimeInterface) {
            $now = new \DateTime();
        }
        return $this->getLastSubscription() > $now;
    }

    public function getDaysBeforeMembershipExpiration(\DateTimeInterface $now = null)
    {
        if (!$now instanceof \DateTimeInterface) {
            $now = new \DateTime();
        }

        $lastSubscription = $this->getLastSubscription();

        if (null === $lastSubscription) {
            return null;
        }

        return $this->getLastSubscription()->diff($now)->days;
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

    public function canAccessAdmin(): int
    {
        $roles = $this->getRoles();

        // TODO ça serait mieux d'avoir une liste d'inclusion des roles admin au lieu d'avoir une liste d'exclusion
        $diff = array_diff($roles, ['ROLE_USER', 'ROLE_COMPANY_MANAGER', 'ROLE_MEMBER_EXPIRED']);

        return count($diff);
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
        if ($this->level === self::LEVEL_ADMIN) {
            $defaultRoles[] = 'ROLE_SUPER_ADMIN';
        }
        if (isset($this->levelModules[0]) && (int) $this->levelModules[0] > 0) {
            $defaultRoles[] = 'ROLE_APERO';
        }
        if (isset($this->levelModules[1]) && (int) $this->levelModules[1] > 0) {
            $defaultRoles[] = 'ROLE_ANNUAIRE';
        }
        if (isset($this->levelModules[2]) && (int) $this->levelModules[2] > 0) {
            $defaultRoles[] = 'ROLE_SITE';
        }
        if (isset($this->levelModules[3]) && (int) $this->levelModules[3] > 0) {
            $defaultRoles[] = 'ROLE_FORUM';
        }
        if (isset($this->levelModules[4]) && (int) $this->levelModules[4] > 0) {
            $defaultRoles[] = 'ROLE_ANTENNE';
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

    public function setRoles(array $roles = null): self
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

    /**
     * @deprecated
     * This method can be removed in Symfony 6.0 - is not needed for apps that do not check user passwords.
     */
    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
        ];
    }

    public function __unserialize($serialized): void
    {
        $this->id = $serialized['id'];
        $this->username = $serialized['username'];
        $this->password = $serialized['password'];
    }

    public function getDirectoryLevel()
    {
        return $this->levelModules[1];
    }

    public function setDirectoryLevel($level): self
    {
        $oldLevelModules = $this->levelModules;
        $this->levelModules[1] = $level;
        $this->propertyChanged('levelModules', $oldLevelModules, $this->levelModules);

        return $this;
    }

    public function getWebsiteLevel()
    {
        return $this->levelModules[2];
    }

    public function setWebsiteLevel($level): void
    {
        $oldLevelModules = $this->levelModules;
        $this->levelModules[2] = $level;
        $this->propertyChanged('levelModules', $oldLevelModules, $this->levelModules);
    }

    public function getEventLevel()
    {
        return $this->levelModules[3];
    }

    public function setEventLevel($level): void
    {
        $oldLevelModules = $this->levelModules;
        $this->levelModules[3] = $level;
        $this->propertyChanged('levelModules', $oldLevelModules, $this->levelModules);
    }

    public function getOfficeLevel()
    {
        return $this->levelModules[4];
    }

    public function setOfficeLevel($level): void
    {
        $oldLevelModules = $this->levelModules;
        $this->levelModules[4] = $level;
        $this->propertyChanged('levelModules', $oldLevelModules, $this->levelModules);
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
}
