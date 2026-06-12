<?php

declare(strict_types=1);

namespace AppBundle\Controller\Planete;

use PlanetePHP\Article;
use PlanetePHP\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ArticlesController
{
    public function __construct(private ArticleRepository $articleRepository) {}

    public function __invoke(Request $request): Response
    {
        $perPage = 20;
        $page = $request->query->getInt("page", 1);

        if ($page < 1) {
            $page = 1;
        }

        $totalCount = $this->articleRepository->countRelevant();
        $articles = $this->articleRepository->findLatest($page - 1, $perPage);

        $data = [];

        foreach ($articles as $article) {
            $data[] = [
                'title' => $article->title,
                'url' => $this->getArticleUrl($article),
                'date' => $article->updatedAt?->format(DATE_RSS),
                'author' => $article->author,
                'content' => $article->content,
                'feed' => [
                    'name' => $article->feed?->name,
                    'url' => $article->feed?->url,
                ],
            ];
        }

        return new Response(
            json_encode($data),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/json',
                'X-Pagination-Total' => $totalCount,
                'X-Pagination-Per-Page' => $perPage,
                'X-Pagination-Has-Next-Page' => json_encode($totalCount > $page * $perPage),
            ],
        );
    }

    private function getArticleUrl(Article $article): string
    {
        $url = $article->url;

        if ($url === null) {
            return '';
        }

        if (!str_starts_with($url, 'http')) {
            $feedUrl = rtrim((string) $article->feed?->url, '/');
            $articleUrl = ltrim($url, '/');

            return implode('/', [$feedUrl, $articleUrl]);
        }

        return $url;
    }
}
