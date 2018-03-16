<?php

namespace AppBundle\TechLetter\Model;

class News implements \JsonSerializable
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
     * @var \DateTimeImmutable
     */
    private $date;

    /**
     * @param string $url
     * @param string $title
     * @param \DateTimeImmutable $date
     */
    public function __construct($url, $title, \DateTimeImmutable $date)
    {
        $this->url = $url;
        $this->title = $title;
        $this->date = $date;
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
     * @return \DateTimeImmutable
     */
    public function getDate()
    {
        return $this->date;
    }

    public function jsonSerialize()
    {
        return [
            'url' => $this->url,
            'title' => $this->title,
            'date' => $this->date->format('Y-m-d')
        ];
    }
}
