<?php

namespace PlanetePHP;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PlaneteIndexAction
{
    /** @var FeedArticleRepository */
    private $billetRepository;
    /** @var FeedRepository */
    private $feedRepository;
    /** @var Environment */
    private $twig;

    public function __construct(
        FeedArticleRepository $billetRepository,
        FeedRepository $feedRepository,
        Environment $twig
    ) {
        $this->billetRepository = $billetRepository;
        $this->feedRepository = $feedRepository;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $page = $request->query->getInt('page');
        if ($page === -1) {
            $page = 1;
        }
        $feedArticles = $this->billetRepository->findLatestTruncated($page);

        return new Response($this->twig->render('index.html.twig', [
            'articles' => $feedArticles,
            'feeds' => $this->feedRepository->getListByLatest(),
            'next' => count($feedArticles) ? $page + 1 : 1,
            'previous' => $page - 1,
        ]));
    }
}
