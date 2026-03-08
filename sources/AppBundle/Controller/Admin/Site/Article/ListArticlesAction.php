<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site\Article;

use AppBundle\Site\Model\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final readonly class ListArticlesAction
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        $fields = ['date', 'titre', 'etat'];

        $sort = $request->query->get('sort', 'date');
        if (in_array($sort, $fields) === false) {
            $sort = 'date';
        }
        $direction = $request->query->get('direction', 'desc');
        $filter = $request->query->get('filter', '');
        $articles = $this->articleRepository->getAllArticlesWithCategoryAndTheme($sort, $direction, $filter);

        return new Response($this->twig->render('admin/site/article_list.html.twig', [
            'articles' => $articles,
            'filter' => $filter,
            'sort' => $sort,
            'direction' => $direction,
        ]));
    }
}
