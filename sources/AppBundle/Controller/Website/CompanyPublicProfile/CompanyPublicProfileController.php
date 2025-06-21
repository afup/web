<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\CompanyPublicProfile;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class CompanyPublicProfileController extends AbstractController
{
    public function __construct(
        private readonly CompanyMemberRepository $companyMemberRepository,
    ) {}

    protected function checkAndGetCompanyMember(int $id, string $slug): CompanyMember
    {
        $companyMember = $this->companyMemberRepository->findById($id);

        if ($companyMember === null
            || $companyMember->getSlug() != $slug
            || false === $companyMember->getPublicProfileEnabled()
            || false === $companyMember->hasUpToDateMembershipFee()
        ) {
            throw $this->createNotFoundException("Company member not found");
        }

        return $companyMember;
    }
}
