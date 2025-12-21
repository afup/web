<?php

declare(strict_types=1);

namespace AppBundle\Controller\Planete;

use PlanetePHP\DisplayableFeedArticle;
use PlanetePHP\FeedArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ArticlesController
{
    public function __construct(private FeedArticleRepository $feedArticleRepository) {}

    public function __invoke(Request $request): Response
    {
        $perPage = 20;
        $page = $request->query->getInt("page", 1);

        if ($page < 1) {
            $page = 1;
        }

        $totalCount = $this->feedArticleRepository->countRelevant();
        $articles = $this->feedArticleRepository->findLatest($page - 1, DATE_RSS, $perPage);

        $data = [];

        foreach ($articles as $article) {
            $data[] = [
                'title' => $article->title,
                'url' => $this->getArticleUrl($article),
                'date' => $article->update,
                'author' => $article->author,
                'content' => $article->content,
                'feed' => [
                    'name' => $article->feedName,
                    'url' => $article->feedUrl,
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

    private function getArticleUrl(DisplayableFeedArticle $article): string
    {
        $url = $article->url;

        if ($url === null) {
            return '';
        }

        if (!str_starts_with($url, 'http')) {
            $feedUrl = rtrim((string) $article->feedUrl, '/');
            $articleUrl = ltrim($url, '/');

            return implode('/', [$feedUrl, $articleUrl]);
        }

        return $url;
    }
}
