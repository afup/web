<?php

declare(strict_types=1);

namespace AppBundle\Controller\Planete;

use PlanetePHP\FeedArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ArticlesController
{
    /** @var FeedArticleRepository */
    private $feedArticleRepository;

    public function __construct(
        FeedArticleRepository $feedArticleRepository
    ) {
        $this->feedArticleRepository = $feedArticleRepository;
    }

    public function __invoke(Request $request): Response
    {
        $page = (int) $request->query->get("page", 1);

        if ($page < 1) {
            $page = 1;
        }

        $articles = $this->feedArticleRepository->findLatest($page - 1, DATE_RSS, 20);

        $data = [];

        foreach ($articles as $article) {
            $data[] = [
                'title' => $article->getTitle(),
                'url' => $article->getUrl(),
                'date' => $article->getUpdate(),
                'author' => $article->getAuthor(),
                'content' => $article->getContent(),
            ];
        }

        return new Response(json_encode($data), 200, ['Content-Type' => 'application/json']);
    }
}
