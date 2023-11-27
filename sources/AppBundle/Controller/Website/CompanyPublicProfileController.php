<?php

namespace AppBundle\Controller\Website;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\UserMembership\BadgesComputer;
use AppBundle\Controller\SiteBaseController;
use AppBundle\Offices\OfficesCollection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CompanyPublicProfileController extends SiteBaseController
{
    public function indexAction($id, $slug)
    {
        $companyMember = $this->checkAndGetCompanyMember($id, $slug);

        return $this->render(
            ':site:company_public_profile.html.twig',
            [
                'company_member' => $companyMember,
                'offices' => $this->getRelatedAfupOffices($companyMember),
                'badges' => $this->get(BadgesComputer::class)->getCompanyBadges($companyMember),
            ]
        );
    }

    /**
     * @param string $id
     * @param string $slug
     *
     * @return CompanyMember
     */
    private function checkAndGetCompanyMember($id, $slug)
    {
        /**
         * @var CompanyMemberRepository $companyRepository
         */
        $companyRepository = $this->get('ting')->get(CompanyMemberRepository::class);
        $companyMember = $companyRepository->findById($id);

        if ($companyMember === null
            || $companyMember->getSlug() != $slug
            || false === $companyMember->getPublicProfileEnabled()
            || false === $companyMember->hasUpToDateMembershipFee()
        ) {
            throw $this->createNotFoundException("Company member not found");
        }

        return $companyMember;
    }

    public function logoAction($id, $slug)
    {
        $companyMember = $this->checkAndGetCompanyMember($id, $slug);

        $dir = $this->getParameter('kernel.project_dir') . '/htdocs/uploads/members_logo';

        $filepath = $dir . DIRECTORY_SEPARATOR . $companyMember->getLogoUrl();

        if (false === is_file($filepath)) {
            throw $this->createNotFoundException();
        }

        return new BinaryFileResponse($filepath);
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
