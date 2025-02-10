<?php

declare(strict_types=1);

namespace AppBundle\TechLetter\Model;

class TechLetter implements \JsonSerializable
{
    private ?News $firstNews;
    private ?News $secondNews;
    private array $articles;
    private array $projects;

    public function __construct(News $firstNews = null, News $secondNews = null, array $articles = [], array $projects = [])
    {
        $this->firstNews = $firstNews;
        $this->secondNews = $secondNews;
        $this->articles = $articles;
        $this->projects = $projects;
    }

    public function getFirstNews(): ?News
    {
        return $this->firstNews;
    }

    public function getSecondNews(): ?News
    {
        return $this->secondNews;
    }

    public function getArticles(): array
    {
        return $this->articles;
    }

    public function getProjects(): array
    {
        return $this->projects;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'firstNews' => $this->firstNews instanceof News ? $this->firstNews->jsonSerialize() : null,
            'secondNews' => $this->secondNews instanceof News ? $this->secondNews->jsonSerialize() : null,
            'articles' => array_map(fn (Article $article) => $article->jsonSerialize(), $this->articles),
            'projects' => array_map(fn (Project $project) => $project->jsonSerialize(), $this->projects)
        ];
    }
}
