<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership\Fee;

use AppBundle\MembershipFee\MembershipFeeInvoicePdfGenerator;
use Afup\Site\Droits;
use AppBundle\Association\MemberType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\AuditLog\Audit;
use AppBundle\MembershipFee\Model\Repository\MembershipFeeRepository;
use AppBundle\Security\MembershipFeeVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class DownloadAction extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly MembershipFeeRepository $membershipFeeRepository,
        private readonly MembershipFeeInvoicePdfGenerator $pdfGenerator,
        private readonly Droits $droits,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): BinaryFileResponse
    {
        $identifiant = $this->droits->obtenirIdentifiant();
        $id = $request->query->getInt('id');

        if (false === $this->isGranted(MembershipFeeVoter::READ_INVOICE, $id)) {
            $this->audit->log("L'utilisateur id: " . $identifiant . ' a tenté de voir la facture id:' . $id);
            throw $this->createAccessDeniedException('Cette facture ne vous appartient pas, vous ne pouvez la visualiser.');
        }

        $tempfile = tempnam(sys_get_temp_dir(), 'membership_fee_download');
        $numeroFacture = $this->pdfGenerator->genererFacture($id, $tempfile);
        $membershipFee = $this->membershipFeeRepository->get($id);

        if ($membershipFee->getUserType() === MemberType::MemberCompany) {
            $company = $this->companyMemberRepository->get($membershipFee->getUserId());
            Assert::isInstanceOf($company, CompanyMember::class);
            $patternPrefix = $company->getCompanyName();
        } else {
            $user = $this->userRepository->get($membershipFee->getUserId());
            Assert::isInstanceOf($user, User::class);
            $patternPrefix = $user->getLastName();
        }

        $pattern = str_replace(' ', '', $patternPrefix) . '_' . $numeroFacture . '_' . $membershipFee->getStartDate()->format('dmY') . '.pdf';

        $response = new BinaryFileResponse($tempfile, Response::HTTP_OK, [], false);
        $response->deleteFileAfterSend(true);
        $response->setContentDisposition('attachment', $pattern);

        return $response;
    }
}
