<?php

namespace AppBundle\Controller\Website;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\UserMembership\BadgesComputer;
use AppBundle\Controller\SiteBaseController;
use AppBundle\Offices\OfficesCollection;

class CompanyPublicProfileController extends SiteBaseController
{
    public function indexAction($id, $slug)
    {
        /**
         * @var $companyRepository CompanyMemberRepository
         */
        $companyRepository = $this->get('ting')->get(CompanyMemberRepository::class);
        $companyMember = $companyRepository->get($id);

        if ($companyMember === null || $companyMember->getSlug() != $slug || false === $companyMember->getPublicProfileEnabled()) {
            throw $this->createNotFoundException("Company member not found");
        }

        return $this->render(
            ':site:company_public_profile.html.twig',
            [
                'company_member' => $companyMember,
                'offices' => $this->getRelatedAfupOffices($companyMember),
                'badges' => $this->get(BadgesComputer::class)->getCompanyBadges($companyMember),
            ]
        );
    }

    private function getRelatedAfupOffices(CompanyMember $companyMember)
    {
        $officesCollection = new OfficesCollection();
        $offices = [];
        foreach ($companyMember->getFormattedRelatedAfupOffices() as $localOffice) {
            $office = $officesCollection->findByCode($localOffice);
            if (null === $office || isset($office['hide_on_offices_page'])) {
                continue;
            }

            $offices[] = $office;
        }

        usort($offices, function ($a, $b) {
            $a = $a['label'];
            $b = $b['label'];

            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });

        return $offices;
    }
}
