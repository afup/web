<?php

declare(strict_types=1);

namespace PlanetePHP;

class FeedArticle
{
    /** @var int */
    private $id;
    /** @var int */
    private $feedId;
    /** @var string */
    private $key;
    /** @var string */
    private $title;
    /** @var string */
    private $url;
    /** @var int */
    private $update;
    /** @var string */
    private $author;
    /** @var string */
    private $summary;
    /** @var string */
    private $content;
    /** @var int */
    private $status;

    /**
     * @param int    $id
     * @param int    $feedId
     * @param string $key
     * @param string $title
     * @param string $url
     * @param int    $update
     * @param string $author
     * @param string $summary
     * @param string $content
     * @param int    $status
     */
    public function __construct(
        $id,
        $feedId,
        $key,
        $title,
        $url,
        $update,
        $author,
        $summary,
        $content,
        $status
    ) {
        $this->id = $id;
        $this->feedId = $feedId;
        $this->key = $key;
        $this->title = $title;
        $this->url = $url;
        $this->update = $update;
        $this->author = $author;
        $this->summary = $summary;
        $this->content = $content;
        $this->status = $status;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFeedId()
    {
        return $this->feedId;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getUpdate()
    {
        return $this->update;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getSummary()
    {
        return $this->summary;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
