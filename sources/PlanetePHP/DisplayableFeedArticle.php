<?php

declare(strict_types=1);

namespace PlanetePHP;

class DisplayableFeedArticle
{
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
        private $title,
        private $url,
        private $update,
        private $author,
        private $content,
        private $feedName,
        private $feedUrl,
    ) {
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
