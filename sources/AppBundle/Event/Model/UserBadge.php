<?php

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class UserBadge implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $badgeId;

    /**
     * @var Badge
     */
    private $badge;

    /**
     * @var \DateTime
     */
    private $issuedAt;

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return $this
     */
    public function setUserId($userId)
    {
        $userId = (int) $userId;
        $this->propertyChanged('userId', $this->userId, $userId);
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return int
     */
    public function getBadgeId()
    {
        return $this->badgeId;
    }

    /**
     * @param int $badgeId
     *
     * @return $this
     */
    public function setBadgeId($badgeId)
    {
        $badgeId = (int) $badgeId;
        $this->propertyChanged('badgeId', $this->badgeId, $badgeId);
        $this->badgeId = $badgeId;

        return $this;
    }

    public function getBadge()
    {
        return $this->badge;
    }

    public function setBadge(Badge $badge = null)
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getIssuedAt()
    {
        return $this->issuedAt;
    }

    /**
     * @param \DateTime $issuedAt
     *
     * @return $this
     */
    public function setIssuedAt(\DateTime $issuedAt = null)
    {
        $this->propertyChanged('issuedAt', $this->issuedAt, $issuedAt);
        $this->issuedAt = $issuedAt;

        return $this;
    }
}
