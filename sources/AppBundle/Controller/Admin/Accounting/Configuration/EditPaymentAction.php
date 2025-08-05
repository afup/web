<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Accounting\Form\OperationType;
use AppBundle\Accounting\Model\Repository\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditPaymentAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private readonly PaymentRepository $paymentRepository,
    ) {}

    public function __invoke(int $id,Request $request): Response
    {
        $payment = $this->paymentRepository->get($id);
        $form = $this->createForm(OperationType::class, $payment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->paymentRepository->save($payment);
            $this->log('Modification du type de règlement ' . $payment->getName());
            $this->addFlash('notice', 'Le type de règlement a été modifié');
            return $this->redirectToRoute('admin_accounting_operations_list');
        }

        return $this->render('admin/accounting/configuration/operation_edit.html.twig', [
            'form' => $form->createView(),
            'payment' => $payment,
            'formTitle' => 'Modifier un type de règlement',
            'submitLabel' => 'Modifier',
        ]);
    }
}
