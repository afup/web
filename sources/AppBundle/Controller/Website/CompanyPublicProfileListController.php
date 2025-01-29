<?php

namespace AppBundle\Controller\Website;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CompanyPublicProfileListController extends Controller
{
    private ViewRenderer $view;

    public function __construct(ViewRenderer $view)
    {
        $this->view = $view;
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

        return $this->view->render(':site:company_public_profile_list.html.twig', [
            'company_member_list' => $displayableCompanies,
        ]);
    }
}
