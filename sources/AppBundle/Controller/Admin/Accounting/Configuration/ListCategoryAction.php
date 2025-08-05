<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Model\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListCategoryAction
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        $categories = $this->categoryRepository->getAllSortedByName();

        return new Response($this->twig->render('admin/accounting/configuration/category_list.html.twig', [
            'categories' => $categories,
        ]));
    }
}
