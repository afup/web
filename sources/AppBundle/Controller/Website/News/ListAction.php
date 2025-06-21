<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\News;

use AppBundle\Site\Form\NewsFiltersType;
use AppBundle\Site\Model\Repository\ArticleRepository;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ListAction extends AbstractController
{
    public const ARTICLES_PER_PAGE = 5;

    public function __construct(
        private readonly ViewRenderer $view,
        private readonly ArticleRepository $articleRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $page = $request->get('page', 1);

        $form = $this->createForm(NewsFiltersType::class);
        $form->handleRequest($request);

        $formData = $form->getData();
        $filters = $formData ?? [];

        return $this->view->render('site/news/list.html.twig', [
            'filters' => $filters,
            'articles' => $this->articleRepository->findPublishedNews($page, self::ARTICLES_PER_PAGE, $filters),
            'total_items' => $this->articleRepository->countPublishedNews($filters),
            'current_page' => $page,
            'articles_per_page' => self::ARTICLES_PER_PAGE,
            'form' => $form->createView(),
        ]);
    }
}
