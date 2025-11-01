<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Form\PaymentType;
use AppBundle\Accounting\Model\Payment;
use AppBundle\Accounting\Model\Repository\PaymentRepository;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddPaymentAction extends AbstractController
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->paymentRepository->save($payment);
            $this->audit->log('Ajout du type de règlement ' . $payment->getName());
            $this->addFlash('notice', 'Le type de règlement a été ajouté');
            return $this->redirectToRoute('admin_accounting_payments_list');
        }

        return $this->render('admin/accounting/configuration/payment_add.html.twig', [
            'form' => $form->createView(),
            'payment' => $payment,
            'formTitle' => 'Ajouter un type de règlement',
            'submitLabel' => 'Ajouter',
        ]);
    }
}
