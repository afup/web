<?php

declare(strict_types=1);

namespace AppBundle\Security\ActionThrottling;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class Log implements NotifyPropertyInterface
{
    const ACTION_SPONSOR_TOKEN = 'sponsor_token';
    const LIMITATIONS = [
        self::ACTION_SPONSOR_TOKEN => ['delay' => 'PT1H', 'limit' => 10],
    ];

    use NotifyProperty;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var string
     */
    private $action;

    /**
     * @var int
     */
    private $objectId;

    private ?\DateTime $createdOn = null;

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
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip): self
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action): self
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return int
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @param int $objectId
     */
    public function setObjectId($objectId): self
    {
        $this->objectId = $objectId;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedOn(): ?\DateTime
    {
        return $this->createdOn;
    }

    public function setCreatedOn(\DateTime $createdOn): self
    {
        $this->createdOn = $createdOn;
        return $this;
    }
}
