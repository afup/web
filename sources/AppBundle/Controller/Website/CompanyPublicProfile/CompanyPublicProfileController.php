<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\CompanyPublicProfile;

use AppBundle\Association\Entity\CompanyMember;
use AppBundle\Association\Entity\Repository\CompanyMemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class CompanyPublicProfileController extends AbstractController
{
    public function __construct(private readonly CompanyMemberRepository $companyMemberRepository) {}

    protected function checkAndGetCompanyMember(int $id, string $slug): CompanyMember
    {
        $companyMember = $this->companyMemberRepository->findById($id);

        if ($companyMember === null
            || $companyMember->getSlug() != $slug
            || true !== $companyMember->publicProfileEnabled
            || false === $companyMember->hasUpToDateMembershipFee()
        ) {
            throw $this->createNotFoundException("Company member not found");
        }

        return $companyMember;
    }
}
