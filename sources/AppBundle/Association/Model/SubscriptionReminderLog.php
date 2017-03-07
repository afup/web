<?php


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

    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $userType;

    /**
     * @var string
     */
    private $reminderKey;

    /**
     * @var \DateTime
     */
    private $reminderDate;

    /**
     * @var boolean
     */
    private $mailSent;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return SubscriptionReminderLog
     */
    public function setId($id)
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return SubscriptionReminderLog
     */
    public function setUserId($userId)
    {
        $userId = (int)$userId;
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
     * @return SubscriptionReminderLog
     */
    public function setEmail($email)
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
     * @return SubscriptionReminderLog
     */
    public function setUserType($userType)
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
     * @return SubscriptionReminderLog
     */
    public function setReminderKey($reminderKey)
    {
        $this->propertyChanged('reminderKey', $this->reminderKey, $reminderKey);
        $this->reminderKey = $reminderKey;
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
     * @return SubscriptionReminderLog
     */
    public function setReminderDate(\DateTime $reminderDate)
    {
        $this->propertyChanged('reminderDate', $this->reminderDate, $reminderDate);
        $this->reminderDate = $reminderDate;
        return $this;
    }

    /**
     * @return bool
     */
    public function getMailSent()
    {
        return $this->mailSent;
    }

    /**
     * @param bool $mailSent
     * @return SubscriptionReminderLog
     */
    public function setMailSent($mailSent)
    {
        $this->propertyChanged('mailSent', $this->mailSent, (bool)$mailSent);
        $this->mailSent = (bool) $mailSent;
        return $this;
    }
}
