<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Accounting\Form\OperationType;
use AppBundle\Accounting\Model\Operation;
use AppBundle\Accounting\Model\Repository\OperationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddOperationAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private readonly OperationRepository $operationRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $operation = new Operation();
        $form = $this->createForm(OperationType::class, $operation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->operationRepository->save($operation);
            $this->log('Ajout de l\'opération ' . $operation->getName());
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
