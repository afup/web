<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\CompanyPublicProfile;

use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class LogoAction extends CompanyPublicProfileController
{
    public function __construct(
        CompanyMemberRepository $companyMemberRepository,
        #[Autowire('%app.members_logo_dir%')]
        private readonly string $storageDir,
    ) {
        parent::__construct($companyMemberRepository);
    }

    public function __invoke(int $id, string $slug): BinaryFileResponse
    {
        $companyMember = $this->checkAndGetCompanyMember($id, $slug);

        $filepath = $this->storageDir . DIRECTORY_SEPARATOR . $companyMember->getLogoUrl();

        if (false === is_file($filepath)) {
            throw $this->createNotFoundException();
        }

        return new BinaryFileResponse($filepath);
    }
}
