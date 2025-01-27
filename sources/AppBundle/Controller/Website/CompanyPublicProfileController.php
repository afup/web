<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\UserMembership\BadgesComputer;
use AppBundle\Offices\OfficesCollection;
use AppBundle\Twig\ViewRenderer;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class CompanyPublicProfileController extends AbstractController
{
    private ViewRenderer $view;
    private BadgesComputer $badgesComputer;
    private RepositoryFactory $repositoryFactory;
    private string $storageDir;

    public function __construct(ViewRenderer $view,
                                BadgesComputer $badgesComputer,
                                RepositoryFactory $repositoryFactory,
                                string $storageDir)
    {
        $this->view = $view;
        $this->badgesComputer = $badgesComputer;
        $this->repositoryFactory = $repositoryFactory;
        $this->storageDir = $storageDir;
    }

    public function index($id, $slug): Response
    {
        $companyMember = $this->checkAndGetCompanyMember($id, $slug);

        return $this->view->render('site/company_public_profile.html.twig', [
            'company_member' => $companyMember,
            'offices' => $this->getRelatedAfupOffices($companyMember),
            'badges' => $this->badgesComputer->getCompanyBadges($companyMember),
        ]);
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
        $companyRepository = $this->repositoryFactory->get(CompanyMemberRepository::class);
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

    public function logo($id, $slug)
    {
        $companyMember = $this->checkAndGetCompanyMember($id, $slug);

        $filepath = $this->storageDir . DIRECTORY_SEPARATOR . $companyMember->getLogoUrl();

        if (false === is_file($filepath)) {
            throw $this->createNotFoundException();
        }

        return new BinaryFileResponse($filepath);
    }

    /**
     * @return mixed[]
     */
    private function getRelatedAfupOffices(CompanyMember $companyMember): array
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

        usort($offices, function (array $a, array $b): int {
            $a = $a['label'];
            $b = $b['label'];
            return $a <=> $b;
        });

        return $offices;
    }
}
