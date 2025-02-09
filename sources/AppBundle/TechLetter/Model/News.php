<?php

declare(strict_types=1);

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
    private \DateTimeImmutable $date;

    /**
     * @param string $url
     * @param string $title
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

    public function getDate(): \DateTimeImmutable
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
