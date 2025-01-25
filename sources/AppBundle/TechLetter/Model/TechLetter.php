<?php

namespace AppBundle\TechLetter\Model;

class TechLetter implements \JsonSerializable
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

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'firstNews' => $this->firstNews ? $this->firstNews->jsonSerialize() : null,
            'secondNews' => $this->secondNews ? $this->secondNews->jsonSerialize() : null,
            'articles' => array_map(fn (Article $article) => $article->jsonSerialize(), $this->articles),
            'projects' => array_map(fn (Project $project) => $project->jsonSerialize(), $this->projects)
        ];
    }
}
