<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Form\PaymentType;
use AppBundle\Accounting\Entity\Repository\PaymentRepository;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditPaymentAction extends AbstractController
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(int $id,Request $request): Response
    {
        $payment = $this->paymentRepository->find($id);
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->paymentRepository->save($payment);
            $this->audit->log('Modification du type de règlement ' . $payment->name);
            $this->addFlash('notice', 'Le type de règlement a été modifié');
            return $this->redirectToRoute('admin_accounting_payments_list');
        }

        return $this->render('admin/accounting/configuration/payment_edit.html.twig', [
            'form' => $form->createView(),
            'payment' => $payment,
            'formTitle' => 'Modifier un type de règlement',
            'submitLabel' => 'Modifier',
        ]);
    }
}
