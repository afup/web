<?php

declare(strict_types=1);

namespace PlanetePHP;

class Feed
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * @param int    $id
     * @param string $name
     * @param string $url
     * @param string $feed
     * @param int    $status
     * @param int    $userId
     */
    public function __construct(
        private $id,
        private $name,
        private $url,
        private $feed,
        private $status,
        private $userId,
    ) {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getFeed()
    {
        return $this->feed;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getUserId()
    {
        return $this->userId;
    }
}
