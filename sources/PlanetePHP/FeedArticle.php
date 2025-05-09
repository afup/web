<?php

declare(strict_types=1);

namespace PlanetePHP;

class FeedArticle
{
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
        private $id,
        private $feedId,
        private $key,
        private $title,
        private $url,
        private $update,
        private $author,
        private $summary,
        private $content,
        private $status,
    ) {
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
