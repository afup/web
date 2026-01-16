<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Form\OperationType;
use AppBundle\Accounting\Entity\Repository\OperationRepository;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditOperationAction extends AbstractController
{
    public function __construct(
        private readonly OperationRepository $operationRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(int $id,Request $request): Response
    {
        $operation = $this->operationRepository->find($id);
        $form = $this->createForm(OperationType::class, $operation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->operationRepository->save($operation);
            $this->audit->log('Modification de l\'opération ' . $operation->name);
            $this->addFlash('notice', 'L\'opération a été modifiée');
            return $this->redirectToRoute('admin_accounting_operations_list');
        }

        return $this->render('admin/accounting/configuration/operation_edit.html.twig', [
            'form' => $form->createView(),
            'account' => $operation,
            'formTitle' => 'Modifier une opération',
            'submitLabel' => 'Modifier',
        ]);
    }
}
