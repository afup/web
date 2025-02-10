<?php

declare(strict_types=1);

namespace PlanetePHP;

class Feed
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $url;
    /** @var string */
    private $feed;
    /** @var int */
    private $status;
    /** @var int */
    private $userId;

    /**
     * @param int    $id
     * @param string $name
     * @param string $url
     * @param string $feed
     * @param int    $status
     * @param int    $userId
     */
    public function __construct($id, $name, $url, $feed, $status, $userId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->feed = $feed;
        $this->status = $status;
        $this->userId = $userId;
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
