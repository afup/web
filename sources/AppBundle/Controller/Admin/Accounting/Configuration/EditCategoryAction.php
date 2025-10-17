<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Form\CategoryType;
use AppBundle\Accounting\Model\Repository\CategoryRepository;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditCategoryAction extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(int $id,Request $request): Response
    {
        $category = $this->categoryRepository->get($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryRepository->save($category);
            $this->audit->log('Modification de la catégorie ' . $category->getName());
            $this->addFlash('notice', 'La catégorie a été modifiée');
            return $this->redirectToRoute('admin_accounting_categories_list');
        }

        return $this->render('admin/accounting/configuration/category_edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'formTitle' => 'Modifier une catégorie',
            'submitLabel' => 'Modifier',
        ]);
    }
}
