<?php

declare(strict_types=1);

namespace AppBundle\TechLetter\Model;

class TechLetter implements \JsonSerializable
{
    private ?News $firstNews;
    private ?News $secondNews;

    /** @var array<Article> */
    private array $articles;

    /** @var array<Project> */
    private array $projects;

    /**
     * @param array<Article> $articles
     * @param array<Project> $projects
     */
    public function __construct(?News $firstNews = null, ?News $secondNews = null, array $articles = [], array $projects = [])
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

    /**
     * @return array<Article>
     */
    public function getArticles(): array
    {
        return $this->articles;
    }

    /**
     * @return array<Project>
     */
    public function getProjects(): array
    {
        return $this->projects;
    }

    public function jsonSerialize(): array
    {
        return [
            'firstNews' => $this->firstNews instanceof News ? $this->firstNews->jsonSerialize() : null,
            'secondNews' => $this->secondNews instanceof News ? $this->secondNews->jsonSerialize() : null,
            'articles' => array_map(fn (Article $article) => $article->jsonSerialize(), $this->articles),
            'projects' => array_map(fn (Project $project) => $project->jsonSerialize(), $this->projects)
        ];
    }
}
