<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership;

use Afup\Site\Association\Cotisations;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Compta\BankAccount\BankAccountFactory;
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
        private readonly Cotisations $cotisations,
    ) {}

    public function __invoke(string $invoiceNumber, ?string $token): Response
    {
        $invoice = $this->cotisations->getByInvoice($invoiceNumber, $token);
        $company = $this->companyMemberRepository->get($invoice['id_personne']);

        if (!$invoice || $company === null) {
            throw $this->createNotFoundException(sprintf('Could not find the invoice "%s" with token "%s"', $invoiceNumber, $token));
        }

        $payboxBilling = new PayboxBilling($company->getFirstName(), $company->getLastName(), $company->getAddress(), $company->getZipCode(), $company->getCity(), $company->getCountry());

        $paybox = $this->payboxFactory->createPayboxForSubscription(
            'F' . $invoiceNumber,
            (float) $invoice['montant'],
            $company->getEmail(),
            $payboxBilling,
        );

        $bankAccountFactory = new BankAccountFactory();

        return $this->view->render('site/company_membership/payment.html.twig', [
            'paybox' => $paybox,
            'invoice' => $invoice,
            'bankAccount' => $bankAccountFactory->createApplyableAt(new \DateTimeImmutable('@' . $invoice['date_debut'])),
            'afup' => [
                'raison_sociale' => AFUP_RAISON_SOCIALE,
                'adresse' => AFUP_ADRESSE,
                'code_postal' => AFUP_CODE_POSTAL,
                'ville' => AFUP_VILLE,
            ],
        ]);
    }
}
