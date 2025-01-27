<?php

namespace AppBundle\Controller\Website;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\WebsiteBlocks;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CompanyPublicProfileListController extends Controller
{
    private WebsiteBlocks $websiteBlocks;

    public function __construct(WebsiteBlocks $websiteBlocks)
    {
        $this->websiteBlocks = $websiteBlocks;
    }

    public function indexAction()
    {
        /**
         * @var CompanyMemberRepository $companyRepository
         */
        $companyRepository = $this->get('ting')->get(CompanyMemberRepository::class);

        $displayableCompanies = $companyRepository->findDisplayableCompanies();

        usort($displayableCompanies, function (CompanyMember $companyMemberA, CompanyMember $companyMemberB) {
            $a = $companyMemberA->getCompanyName();
            $b = $companyMemberB->getCompanyName();
            return $a <=> $b;
        });

        return $this->websiteBlocks->render(':site:company_public_profile_list.html.twig', [
            'company_member_list' => $displayableCompanies,
        ]);
    }
}
