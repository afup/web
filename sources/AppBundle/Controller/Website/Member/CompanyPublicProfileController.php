<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Member;

use AppBundle\Association\Form\CompanyPublicProfile;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\User;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class CompanyPublicProfileController extends AbstractController
{
    public function __construct(
        private readonly RepositoryFactory $repositoryFactory,
        private readonly string $storageDir,
    ) {
    }
    public function index(Request $request)
    {
        /**
         * @var CompanyMemberRepository $companyRepository
         */
        $companyRepository = $this->repositoryFactory->get(CompanyMemberRepository::class);
        $companyMember = null;
        if ($this->getUser() instanceof User) {
            /** @var CompanyMember $companyMember */
            $companyMember = $companyRepository->get($this->getUser()->getCompanyId());
        }

        if ($companyMember === null) {
            throw $this->createNotFoundException("Company member not found");
        }

        $defaultData = [
            'enabled' => $companyMember->getPublicProfileEnabled(),
            'description' => $companyMember->getDescription(),
            'website_url' => $companyMember->getWebsiteUrl(),
            'contact_page_url' => $companyMember->getContactPageUrl(),
            'careers_page_url' => $companyMember->getCareersPageUrl(),
            'twitter_handle' => $companyMember->getTwitterHandle(),
            'related_afup_offices' => $companyMember->getFormattedRelatedAfupOffices(),
            'membership_reason' => $companyMember->getMembershipReason(),
        ];

        $formOptions = [
            'logo_required' => false === $companyMember->hasLogoUrl(),
        ];

        $form = $this->createForm(CompanyPublicProfile::class, $defaultData, $formOptions);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $data['logo'];

            if (null !== $uploadedFile) {
                $filename = $companyMember->getId() . '.' . $uploadedFile->getClientOriginalExtension();

                $data['logo']->move(
                    $this->prepareUploadedFilesDir(),
                    $filename
                );

                $companyMember->setLogoUrl($filename);
            }

            $companyMember
                ->setPublicProfileEnabled($data['enabled'])
                ->setDescription($data[('description')])
                ->setWebsiteUrl($data['website_url'])
                ->setContactPageUrl($data['contact_page_url'])
                ->setCareersPageUrl($data['careers_page_url'])
                ->setTwitterHandle($data['twitter_handle'])
                ->setFormattedRelatedAfupOffices($data['related_afup_offices'])
                ->setMembershipReason($data['membership_reason'])
            ;

            $companyRepository->save($companyMember);

            $this->addFlash('success', 'Modifications enregistrées');
            return $this->redirectToRoute('member_company_public_profile');
        }

        return $this->render(
            'site/member/company_public_profile.html.twig',
            [
                'form' => $form->createView(),
                'company_member' => $companyMember,
            ]
        );
    }

    private function prepareUploadedFilesDir(): string
    {
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir);
        }

        return $this->storageDir;
    }
}
