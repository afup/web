<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\News;

use AppBundle\Pagination\SeoPagination;
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

        $formData = $form->getData();
        $filters = $formData ?? [];

        $lastPage = SeoPagination::getLastPage(
            $this->articleRepository->countPublishedArticles($filters),
            self::ARTICLES_PER_PAGE,
        );

        if ($page < 1 || $page > $lastPage) {
            throw $this->createNotFoundException(sprintf('La page %d n\'existe pas.', $page));
        }

        return $this->view->render('site/news/list.html.twig', [
            'filters' => $filters,
            'articles' => $this->articleRepository->findPublishedArticles($page, self::ARTICLES_PER_PAGE, $filters),
            'current_page' => $page,
            'last_page' => $lastPage,
            'pages' => SeoPagination::getPages($page, $lastPage),
            'form' => $form->createView(),
        ]);
    }
}
