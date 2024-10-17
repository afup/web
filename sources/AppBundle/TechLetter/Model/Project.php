<?php

declare(strict_types=1);

namespace AppBundle\TechLetter\Model;

class Project implements \JsonSerializable
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @param string $url
     * @param string $name
     * @param string $description
     */
    public function __construct($url, $name, $description)
    {
        $this->url = $url;
        $this->name = $name;
        $this->description = $description;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'url' => $this->url,
            'name' => $this->name,
            'description' => $this->description
        ];
    }
}
