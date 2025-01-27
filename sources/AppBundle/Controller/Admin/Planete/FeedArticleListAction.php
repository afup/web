<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Planete;

use PlanetePHP\FeedArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class FeedArticleListAction
{
    private FeedArticleRepository $feedArticleRepository;
    private Environment $twig;

    public function __construct(
        FeedArticleRepository $feedArticleRepository,
        Environment $twig
    ) {
        $this->feedArticleRepository = $feedArticleRepository;
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        $sort = $request->query->get('sort', 'title');
        $direction = $request->query->get('direction', 'asc');

        return new Response($this->twig->render('admin/planete/feed_article_list.html.twig', [
            'articles' => $this->feedArticleRepository->search($sort, $direction, 20),
            'sort' => $sort,
            'direction' => $direction,
        ]));
    }
}
