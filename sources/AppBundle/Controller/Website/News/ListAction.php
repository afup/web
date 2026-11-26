<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\News;

use AppBundle\Pagination\SeoPagination;
use AppBundle\Site\Enum\ArticleTheme;
use AppBundle\Site\Form\NewsFiltersType;
use AppBundle\Site\Entity\Repository\ArticleRepository;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ListAction extends AbstractController
{
    public const int ARTICLES_PER_PAGE = 5;

    public function __construct(
        private readonly ViewRenderer $view,
        private readonly ArticleRepository $articleRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $form = $this->createForm(NewsFiltersType::class);
        $form->handleRequest($request);

        // Une valeur de filtre inconnue (année, thème, événement) : ce n'est pas une liste qui existe.
        if ($form->isSubmitted() && !$form->isValid()) {
            throw $this->createNotFoundException('Filtre inconnu.');
        }

        /** @var array{year?: list<int|string>, theme?: list<ArticleTheme>, event?: list<int|string>} $filters */
        $filters = $form->getData() ?? [];
        $queryFilters = self::toQueryParameters($filters);
        $count = $this->articleRepository->countPublishedArticles($filters);

        if ($queryFilters !== [] && $count === 0) {
            throw $this->createNotFoundException('Aucune actualité pour ces filtres.');
        }

        $lastPage = SeoPagination::getLastPage($count, self::ARTICLES_PER_PAGE);

        if ($page < 1 || $page > $lastPage) {
            throw $this->createNotFoundException(sprintf('La page %d n\'existe pas.', $page));
        }

        return $this->view->render('site/news/list.html.twig', [
            'query_filters' => $queryFilters,
            'is_filtered' => $queryFilters !== [],
            'articles' => $this->articleRepository->findPublishedArticles($page, self::ARTICLES_PER_PAGE, $filters),
            'current_page' => $page,
            'last_page' => $lastPage,
            'pages' => SeoPagination::getPages($page, $lastPage),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Les filtres tels qu'ils figurent dans les URLs de la liste (pager, canonical) : des valeurs scalaires,
     * les thèmes par leur valeur et non en objets, dans un ordre fixe, pour qu'une même liste n'ait qu'une URL.
     *
     * @param array{year?: list<int|string>, theme?: list<ArticleTheme>, event?: list<int|string>} $filters
     *
     * @return array<'year'|'theme'|'event', non-empty-list<int>>
     */
    private static function toQueryParameters(array $filters): array
    {
        $parameters = [
            'year' => array_map(intval(...), $filters['year'] ?? []),
            'theme' => array_map(static fn(ArticleTheme $theme): int => $theme->value, $filters['theme'] ?? []),
            'event' => array_map(intval(...), $filters['event'] ?? []),
        ];
        foreach ($parameters as &$values) {
            sort($values);
        }

        return array_filter($parameters);
    }
}
