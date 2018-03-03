<?php

namespace AppBundle\TechLetter\Model;

class TechLetter
{
    /**
     * @var News|null
     */
    private $firstNews;
    /**
     * @var News|null
     */
    private $secondNews;
    /**
     * @var array
     */
    private $articles;
    /**
     * @var array
     */
    private $projects;

    public function __construct(News $firstNews = null, News $secondNews = null, array $articles = [], array $projects = [])
    {
        $this->firstNews = $firstNews;
        $this->secondNews = $secondNews;
        $this->articles = $articles;
        $this->projects = $projects;
    }

    /**
     * @return News|null
     */
    public function getFirstNews()
    {
        return $this->firstNews;
    }

    /**
     * @return News|null
     */
    public function getSecondNews()
    {
        return $this->secondNews;
    }

    /**
     * @return array
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * @return array
     */
    public function getProjects()
    {
        return $this->projects;
    }
}
