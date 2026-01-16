<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Entity\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final readonly class ListCategoryAction
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        $categories = $this->categoryRepository->getAllSortedByName();

        return new Response($this->twig->render('admin/accounting/configuration/category_list.html.twig', [
            'categories' => $categories,
        ]));
    }
}
