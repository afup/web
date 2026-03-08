<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Entity\Repository\PaymentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListPaymentAction
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository,
        private readonly Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        $payments = $this->paymentRepository->getAllSortedByName();

        return new Response($this->twig->render('admin/accounting/configuration/payment_list.html.twig', [
            'payments' => $payments,
        ]));
    }
}
