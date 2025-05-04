<?php

declare(strict_types=1);

namespace AppBundle\TechLetter\Model;

class TechLetter implements \JsonSerializable
{
    /**
     * @param array<Article> $articles
     * @param array<Project> $projects
     */
    public function __construct(
        private readonly ?News $firstNews = null,
        private readonly ?News $secondNews = null,
        private readonly array $articles = [],
        private readonly array $projects = [],
    ) {
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
            'articles' => array_map(fn (Article $article): array => $article->jsonSerialize(), $this->articles),
            'projects' => array_map(fn (Project $project): array => $project->jsonSerialize(), $this->projects),
        ];
    }
}
