<?php

namespace AppBundle\Controller\Website;

use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Controller\SiteBaseController;

class CompanyPublicProfileListController extends SiteBaseController
{
    public function indexAction()
    {
        /**
         * @var $companyRepository CompanyMemberRepository
         */
        $companyRepository = $this->get('ting')->get(CompanyMemberRepository::class);

        return $this->render(
            ':site:company_public_profile_list.html.twig',
            [
                'company_member_list' => $companyRepository->findDisplayableCompanies(),
            ]
        );
    }
}
