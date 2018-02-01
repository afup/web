<?php

namespace AppBundle\Association\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class TechletterSubscription implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var \DateTime
     */
    private $subscriptionDate;

    /**
     * @var User
     */
    private $user;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TechletterSubscription
     */
    public function setId($id)
    {
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
     * @return TechletterSubscription
     */
    public function setUserId($userId)
    {
        $userId = (int)$userId;
        $this->propertyChanged('userId', $this->userId, $userId);
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSubscriptionDate()
    {
        return $this->subscriptionDate;
    }

    /**
     * @param \DateTime $subscriptionDate
     * @return TechletterSubscription
     */
    public function setSubscriptionDate(\DateTime $subscriptionDate)
    {
        $this->propertyChanged('subscriptionDate', $this->subscriptionDate, $subscriptionDate);
        $this->subscriptionDate = $subscriptionDate;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return TechletterSubscription
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }
}
