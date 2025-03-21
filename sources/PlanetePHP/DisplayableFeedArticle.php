<?php

declare(strict_types=1);

namespace PlanetePHP;

class DisplayableFeedArticle
{
    /** @var string */
    private $title;
    /** @var string */
    private $url;
    /** @var string */
    private $update;
    /** @var string */
    private $author;
    /** @var string */
    private $content;
    /** @var string */
    private $feedName;
    /** @var string */
    private $feedUrl;

    /**
     * @param string $title
     * @param string $url
     * @param string $update
     * @param string $author
     * @param string $content
     * @param string $feedName
     * @param string $feedUrl
     */
    public function __construct(
        $title,
        $url,
        $update,
        $author,
        $content,
        $feedName,
        $feedUrl
    ) {
        $this->title = $title;
        $this->url = $url;
        $this->update = $update;
        $this->author = $author;
        $this->content = $content;
        $this->feedName = $feedName;
        $this->feedUrl = $feedUrl;
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

    public function getContent()
    {
        return $this->content;
    }

    public function getFeedName()
    {
        return $this->feedName;
    }

    public function getFeedUrl()
    {
        return $this->feedUrl;
    }

    public function setContent($content): void
    {
        $this->content = $content;
    }
}
