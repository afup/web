<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership\Fee;

use Afup\Site\Association\Cotisations;
use Afup\Site\Droits;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\AuditLog\Audit;
use Assert\Assertion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DownloadAction extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly Cotisations $cotisations,
        private readonly Droits $droits,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): BinaryFileResponse
    {
        $identifiant = $this->droits->obtenirIdentifiant();
        $id = $request->get('id');

        if (false === $this->cotisations->isCurrentUserAllowedToReadInvoice($id)) {
            $this->audit->log("L'utilisateur id: " . $identifiant . ' a tenté de voir la facture id:' . $id);
            throw $this->createAccessDeniedException('Cette facture ne vous appartient pas, vous ne pouvez la visualiser.');
        }

        $tempfile = tempnam(sys_get_temp_dir(), 'membership_fee_download');
        $numeroFacture = $this->cotisations->genererFacture($id, $tempfile);
        $cotisation = $this->cotisations->obtenir($id);

        if ($cotisation['type_personne'] == AFUP_PERSONNES_MORALES) {
            $company = $this->companyMemberRepository->get($cotisation['id_personne']);
            Assertion::isInstanceOf($company, CompanyMember::class);
            $patternPrefix = $company->getCompanyName();
        } else {
            $user = $this->userRepository->get($cotisation['id_personne']);
            Assertion::isInstanceOf($user, User::class);
            $patternPrefix = $user->getLastName();
        }

        $pattern = str_replace(' ', '', $patternPrefix) . '_' . $numeroFacture . '_' . date('dmY', (int) $cotisation['date_debut']) . '.pdf';

        $response = new BinaryFileResponse($tempfile, Response::HTTP_OK, [], false);
        $response->deleteFileAfterSend(true);
        $response->setContentDisposition('attachment', $pattern);

        return $response;
    }
}
