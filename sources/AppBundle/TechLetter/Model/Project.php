<?php

namespace AppBundle\TechLetter\Model;

class Project
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
}
