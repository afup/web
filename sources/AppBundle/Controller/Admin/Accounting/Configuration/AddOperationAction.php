<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Form\OperationType;
use AppBundle\Accounting\Entity\Operation;
use AppBundle\Accounting\Entity\Repository\OperationRepository;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddOperationAction extends AbstractController
{
    public function __construct(
        private readonly OperationRepository $operationRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $operation = new Operation();
        $form = $this->createForm(OperationType::class, $operation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->operationRepository->save($operation);
            $this->audit->log('Ajout de l\'opération ' . $operation->name);
            $this->addFlash('notice', 'L\'opération a été ajoutée');
            return $this->redirectToRoute('admin_accounting_operations_list');
        }

        return $this->render('admin/accounting/configuration/operation_add.html.twig', [
            'form' => $form->createView(),
            'operation' => $operation,
            'formTitle' => 'Ajouter une opération',
            'submitLabel' => 'Ajouter',
        ]);
    }
}
