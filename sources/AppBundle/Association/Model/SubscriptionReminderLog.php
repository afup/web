<?php

declare(strict_types=1);


namespace AppBundle\Association\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class SubscriptionReminderLog implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    private ?int $userId = null;

    /**
     * @var int
     */
    private $userType;

    /**
     * @var string
     */
    private $reminderKey;

    private ?\DateTime $reminderDate = null;

    private ?bool $mailSent = null;

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
     * @return int
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId): self
    {
        $userId = (int) $userId;
        $this->propertyChanged('userId', $this->userId, $userId);
        $this->userId = $userId;
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
     * @return int
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * @param int $userType
     */
    public function setUserType($userType): self
    {
        $this->propertyChanged('userType', $this->userType, $userType);
        $this->userType = $userType;
        return $this;
    }

    /**
     * @return string
     */
    public function getReminderKey()
    {
        return $this->reminderKey;
    }

    /**
     * @param string $reminderKey
     */
    public function setReminderKey($reminderKey): self
    {
        $this->propertyChanged('reminderKey', $this->reminderKey, $reminderKey);
        $this->reminderKey = $reminderKey;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getReminderDate(): ?\DateTime
    {
        return $this->reminderDate;
    }

    public function setReminderDate(\DateTime $reminderDate): self
    {
        $this->propertyChanged('reminderDate', $this->reminderDate, $reminderDate);
        $this->reminderDate = $reminderDate;
        return $this;
    }

    /**
     * @return bool
     */
    public function getMailSent(): ?bool
    {
        return $this->mailSent;
    }

    /**
     * @param bool $mailSent
     */
    public function setMailSent($mailSent): self
    {
        $this->propertyChanged('mailSent', $this->mailSent, (bool) $mailSent);
        $this->mailSent = (bool) $mailSent;
        return $this;
    }
}
