<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\CompanyPublicProfile;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class ListAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly CompanyMemberRepository $companyMemberRepository,
    ) {}

    public function index(): Response
    {
        $displayableCompanies = $this->companyMemberRepository->findDisplayableCompanies();

        usort($displayableCompanies, function (CompanyMember $companyMemberA, CompanyMember $companyMemberB): int {
            $a = $companyMemberA->getCompanyName();
            $b = $companyMemberB->getCompanyName();
            return $a <=> $b;
        });

        return $this->view->render('site/company_public_profile_list.html.twig', [
            'company_member_list' => $displayableCompanies,
        ]);
    }
}
