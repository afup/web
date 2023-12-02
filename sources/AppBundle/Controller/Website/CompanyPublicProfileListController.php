<?php

namespace AppBundle\Controller\Website;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Controller\SiteBaseController;

class CompanyPublicProfileListController extends SiteBaseController
{
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

            if ($a == $b) {
                return 0;
            }

            return ($a < $b) ? -1 : 1;
        });

        return $this->render(
            ':site:company_public_profile_list.html.twig',
            [
                'company_member_list' => $displayableCompanies,
            ]
        );
    }
}
