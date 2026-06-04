<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership;

use AppBundle\Afup;
use AppBundle\MembershipFee\MembershipFeeService;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Compta\BankAccount\BankAccountFactory;
use AppBundle\MembershipFee\Model\MembershipFee;
use AppBundle\Payment\PayboxBilling;
use AppBundle\Payment\PayboxFactory;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class PaymentAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly PayboxFactory $payboxFactory,
        private readonly MembershipFeeService $membershipFeeService,
    ) {}

    public function __invoke(string $invoiceNumber, ?string $token): Response
    {
        $invoice = $this->membershipFeeService->getByInvoice($invoiceNumber, $token);
        $company = null;
        if ($invoice instanceof MembershipFee) {
            $company = $this->companyMemberRepository->get($invoice->getUserId());
        }

        if (!$invoice || $company === null) {
            throw $this->createNotFoundException(sprintf('Could not find the invoice "%s" with token "%s"', $invoiceNumber, $token));
        }

        $payboxBilling = new PayboxBilling($company->getFirstName(), $company->getLastName(), $company->getAddress(), $company->getZipCode(), $company->getCity(), $company->getCountry());

        $paybox = $this->payboxFactory->createPayboxForSubscription(
            'F' . $invoiceNumber,
            (float) $invoice->getAmount(),
            $company->getEmail(),
            $payboxBilling,
        );

        $bankAccountFactory = new BankAccountFactory();

        return $this->view->render('site/company_membership/payment.html.twig', [
            'paybox' => $paybox,
            'invoice' => $invoice,
            'bankAccount' => $bankAccountFactory->createApplyableAt(new \DateTimeImmutable('@' . $invoice->getStartDate()->getTimestamp())),
            'afup' => [
                'raison_sociale' => Afup::RAISON_SOCIALE,
                'adresse' => Afup::ADRESSE,
                'code_postal' => Afup::CODE_POSTAL,
                'ville' => Afup::VILLE,
            ],
        ]);
    }
}
