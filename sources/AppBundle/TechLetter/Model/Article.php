<?php

namespace AppBundle\TechLetter\Model;

class Article
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
     * @param string $url
     * @param string $title
     * @param string $host
     * @param int $readingTime
     * @param string $excerpt
     */
    public function __construct($url, $title, $host, $readingTime, $excerpt)
    {
        $this->url = $url;
        $this->title = $title;
        $this->host = $host;
        $this->readingTime = $readingTime;
        $this->excerpt = $excerpt;
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
}
