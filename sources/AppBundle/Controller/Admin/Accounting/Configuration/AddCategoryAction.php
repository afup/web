<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Entity\Category;
use AppBundle\Accounting\Entity\Repository\CategoryRepository;
use AppBundle\Accounting\Form\CategoryType;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddCategoryAction extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryRepository->save($category);
            $this->audit->log('Ajout de la catégorie ' . $category->name);
            $this->addFlash('notice', 'La catégorie a été ajoutée');
            return $this->redirectToRoute('admin_accounting_categories_list');
        }

        return $this->render('admin/accounting/configuration/category_add.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'formTitle' => 'Ajouter une catégorie',
            'submitLabel' => 'Ajouter',
        ]);
    }
}
