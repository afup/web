<?php

declare(strict_types=1);

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

    private ?int $userId = null;

    private ?\DateTime $subscriptionDate = null;

    private ?User $user = null;

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
     * @return \DateTime
     */
    public function getSubscriptionDate(): ?\DateTime
    {
        return $this->subscriptionDate;
    }

    public function setSubscriptionDate(\DateTime $subscriptionDate): self
    {
        $this->propertyChanged('subscriptionDate', $this->subscriptionDate, $subscriptionDate);
        $this->subscriptionDate = $subscriptionDate;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
