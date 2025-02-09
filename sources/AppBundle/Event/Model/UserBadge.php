<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class UserBadge implements NotifyPropertyInterface
{
    use NotifyProperty;

    private ?int $userId = null;

    private ?int $badgeId = null;

    private ?Badge $badge = null;

    private ?\DateTime $issuedAt = null;

    /**
     * @return int
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return $this
     */
    public function setUserId($userId): self
    {
        $userId = (int) $userId;
        $this->propertyChanged('userId', $this->userId, $userId);
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return int
     */
    public function getBadgeId(): ?int
    {
        return $this->badgeId;
    }

    /**
     * @param int $badgeId
     *
     * @return $this
     */
    public function setBadgeId($badgeId): self
    {
        $badgeId = (int) $badgeId;
        $this->propertyChanged('badgeId', $this->badgeId, $badgeId);
        $this->badgeId = $badgeId;

        return $this;
    }

    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    public function setBadge(Badge $badge = null): self
    {
        $this->badge = $badge;

        return $this;
    }

    public function getIssuedAt(): ?\DateTime
    {
        return $this->issuedAt;
    }

    /**
     * @return $this
     */
    public function setIssuedAt(\DateTime $issuedAt = null): self
    {
        $this->propertyChanged('issuedAt', $this->issuedAt, $issuedAt);
        $this->issuedAt = $issuedAt;

        return $this;
    }
}
