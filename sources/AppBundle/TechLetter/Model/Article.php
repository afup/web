<?php

declare(strict_types=1);

namespace AppBundle\TechLetter\Model;

class Article implements \JsonSerializable
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $readingTime;

    /**
     * @var string
     */
    private $excerpt;

    /**
     * @var string
     */
    private $language;

    /**
     * @param string $url
     * @param string $title
     * @param string $host
     * @param int $readingTime
     * @param string $excerpt
     */
    public function __construct($url, $title, $host, $readingTime, $excerpt, $language)
    {
        $this->url = $url;
        $this->title = $title;
        $this->host = $host;
        $this->readingTime = $readingTime;
        $this->excerpt = $excerpt;
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getReadingTime()
    {
        return $this->readingTime;
    }

    /**
     * @return string
     */
    public function getExcerpt()
    {
        return $this->excerpt;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'url' => $this->url,
            'title' => $this->title,
            'host' => $this->host,
            'readingTime' => $this->readingTime,
            'excerpt' => $this->excerpt,
            'language' => $this->language,
        ];
    }
}
