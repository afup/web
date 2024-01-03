<?php

namespace AppBundle\Association\Model;

use AppBundle\Association\NotifiableInterface;
use AppBundle\Offices\OfficesCollection;
use AppBundle\Validator\Constraints as AppAssert;
use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @AppAssert\UniqueEntity(fields={"username"}, repository="\AppBundle\Association\Model\Repository\UserRepository")
 * @AppAssert\UniqueEntity(fields={"email"}, repository="\AppBundle\Association\Model\Repository\UserRepository")
 */
class User implements NotifyPropertyInterface, UserInterface, \Serializable, NotifiableInterface
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
     * @var int
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

    /**
     * @var \DateTime
     */
    private $reminderDate;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var \Datetime
     */
    private $lastSubscription;

    /**
     * @var CompanyMember
     */
    private $company;

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
     * @return User
     */
    public function setId($id)
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
     * @return User
     */
    public function setMobilephone($mobilephone)
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
     * @return User
     */
    public function setNearestOffice($nearestOffice)
    {
        $this->propertyChanged('nearestOffice', $this->nearestOffice, $nearestOffice);
        $this->nearestOffice = $nearestOffice;
        return $this;
    }

    public function getNearestOfficeLabel()
    {
        $code = $this->getNearestOffice();

        // FIXME corriger ça dans le formulaire
        if (null === $code || '-Aucune-' === $code || 0 === strlen(trim($code))) {
            return  null;
        }

        $collection = new OfficesCollection();
        $office = $collection->findByCode($code);

        return $office['label'];
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
     * @return User
     */
    public function setCompanyId($companyId)
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
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->propertyChanged('username', $this->username, $username);
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->propertyChanged('password', $this->password, $password);
        $this->password = $password;
        return $this;
    }

    /**
     * @param string $password
     */
    public function setPlainPassword($password)
    {
        $this->setPassword(md5($password));
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
     * @return User
     */
    public function setLevel($level)
    {
        $this->propertyChanged('level', $this->level, $level);
        $this->level = $level;
        return $this;
    }

    /**
     * @return int
     */
    public function getLevelModules()
    {
        return $this->levelModules;
    }

    /**
     * @param int $levelModules
     * @return User
     */
    public function setLevelModules($levelModules)
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
     * @return User
     */
    public function setCivility($civility)
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
     * @return User
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
     * @return User
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
     * @return User
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
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return User
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
     * @return User
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
     * @return User
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

    public function getCountryIso3166Numeric()
    {
        $country = $this->getCountry();

        try {
            $data = (new \League\ISO3166\ISO3166)->alpha2($country);

            return $data['numeric'];
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @param string $country
     * @return User
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
     * @return User
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
     * @return User
     */
    public function setStatus($status)
    {
        $this->propertyChanged('status', $this->status, $status);
        $this->status = $status;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getReminderDate()
    {
        return $this->reminderDate;
    }

    /**
     * @param \DateTime $reminderDate
     * @return User
     */
    public function setReminderDate(\DateTime $reminderDate = null)
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
     * @return User
     */
    public function setHash($hash)
    {
        $this->propertyChanged('hash', $this->hash, $hash);
        $this->hash = $hash;
        return $this;
    }

    public function getLabel()
    {
        return $this->firstName . ' ' . $this->lastName;
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

    public function getDaysBeforeMembershipExpiration(\DateTimeInterface $now = null)
    {
        if (null === $now) {
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
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param CompanyMember $company
     * @return User
     */
    public function setCompany(CompanyMember $company)
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
     *
     * @return User
     */
    public function setSlackInviteStatus($slackInviteStatus)
    {
        $this->propertyChanged('slackInviteStatus', $this->slackInviteStatus, $slackInviteStatus);
        $this->slackInviteStatus = $slackInviteStatus;

        return $this;
    }

    /**
     * @return bool
     */
    public function canRequestSlackInvite()
    {
        return false === $this->hasRole('ROLE_MEMBER_EXPIRED') && $this->getSlackInviteStatus() === self::SLACK_INVITE_STATUS_NONE;
    }

    /**
     * @return bool
     */
    public function slackInviteRequested()
    {
        return $this->getSlackInviteStatus() === self::SLACK_INVITE_STATUS_REQUESTED;
    }

    public function canAccessAdmin()
    {
        $roles = $this->getRoles();

        // TODO ça serait mieux d'avoir une liste d'inclusion des roles admin au lieu d'avoir une liste d'exclusion
        $diff = array_diff($roles, ['ROLE_USER', 'ROLE_COMPANY_MANAGER', 'ROLE_MEMBER_EXPIRED']);

        return count($diff);
    }

    /**
     * @return boolean
     */
    public function isMemberForCompany()
    {
        return ($this->companyId !== null && $this->companyId > 0);
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        $defaultRoles = ['ROLE_USER'];
        if ($this->lastSubscription < new \DateTime()) {
            $defaultRoles = ['ROLE_MEMBER_EXPIRED'];
        }
        if ($this->level == self::LEVEL_ADMIN) {
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
     * @return bool
     */
    public function hasRole($role)
    {
        $roles = $this->getRoles();
        if (in_array($role, $roles)) {
            return true;
        }

        return false;
    }

    /**
     * @param array $roles
     * @return User
     */
    public function setRoles(array $roles = null)
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
     * @return User
     */
    public function addRole($role)
    {
        $roles = $this->roles;
        $this->roles[] = $role;
        $this->roles = array_unique($this->roles);

        $this->propertyChanged('roles', $roles, $this->roles);

        return $this;
    }

    /**
     * @param string $role
     * @return User
     */
    public function removeRole($role)
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
     * @inheritDoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize([
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password
        ]);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $array = unserialize($serialized);
        $this->id = $array['id'];
        $this->username = $array['username'];
        $this->password = $array['password'];
    }

    public function getDirectoryLevel()
    {
        return $this->levelModules[1];
    }

    public function setDirectoryLevel($level)
    {
        $oldLevelModules = $this->levelModules;
        $this->levelModules[1] = $level;
        $this->propertyChanged('levelModules', $oldLevelModules, $this->levelModules);
    }

    public function getWebsiteLevel()
    {
        return $this->levelModules[2];
    }

    public function setWebsiteLevel($level)
    {
        $oldLevelModules = $this->levelModules;
        $this->levelModules[2] = $level;
        $this->propertyChanged('levelModules', $oldLevelModules, $this->levelModules);
    }

    public function getEventLevel()
    {
        return $this->levelModules[3];
    }

    public function setEventLevel($level)
    {
        $oldLevelModules = $this->levelModules;
        $this->levelModules[3] = $level;
        $this->propertyChanged('levelModules', $oldLevelModules, $this->levelModules);
    }

    public function getOfficeLevel()
    {
        return $this->levelModules[4];
    }

    public function setOfficeLevel($level)
    {
        $oldLevelModules = $this->levelModules;
        $this->levelModules[4] = $level;
        $this->propertyChanged('levelModules', $oldLevelModules, $this->levelModules);
    }

    public function getAlternateEmail()
    {
        return $this->alternateEmail;
    }

    public function setAlternateEmail($alternateEmail)
    {
        $this->propertyChanged('alternateEmail', $this->alternateEmail, $alternateEmail);
        $this->alternateEmail = $alternateEmail;
    }

    public function getNeedsUpToDateMembership()
    {
        return $this->needsUpToDateMembership;
    }

    public function setNeedsUpToDateMembership($needsUpToDateMembership)
    {
        $this->propertyChanged('needsUpToDateMembership', $this->needsUpToDateMembership, $needsUpToDateMembership);
        $this->needsUpToDateMembership = $needsUpToDateMembership;
    }
}
