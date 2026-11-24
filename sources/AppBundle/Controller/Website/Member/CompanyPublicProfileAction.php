<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Member;

use AppBundle\Association\Form\CompanyPublicProfile;
use AppBundle\Association\Entity\Repository\CompanyMemberRepository;
use AppBundle\Security\Authentication;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CompanyPublicProfileAction extends AbstractController
{
    public function __construct(
        private readonly CompanyMemberRepository $companyMemberRepository,
        #[Autowire('%app.members_logo_dir%')]
        private readonly string $storageDir,
        private readonly Authentication $authentication,
    ) {}

    public function __invoke(Request $request): Response
    {
        $companyMember = $this->companyMemberRepository->find($this->authentication->getAfupUser()->getCompanyId());

        if ($companyMember === null) {
            throw $this->createNotFoundException("Company member not found");
        }

        $defaultData = [
            'enabled' => (bool) $companyMember->publicProfileEnabled,
            'description' => $companyMember->description,
            'website_url' => $companyMember->websiteUrl,
            'contact_page_url' => $companyMember->contactPageUrl,
            'careers_page_url' => $companyMember->careersPageUrl,
            'twitter_handle' => $companyMember->twitterHandle,
            'related_afup_offices' => $companyMember->getFormattedRelatedAfupOffices(),
            'membership_reason' => $companyMember->membershipReason,
        ];

        $formOptions = [
            'logo_required' => false === $companyMember->hasLogoUrl(),
        ];

        $form = $this->createForm(CompanyPublicProfile::class, $defaultData, $formOptions);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $uploadedFile = $data['logo'] ?? null;

            if ($uploadedFile instanceof UploadedFile) {
                $filename = $companyMember->id . '.' . $uploadedFile->getClientOriginalExtension();

                $uploadedFile->move(
                    $this->prepareUploadedFilesDir(),
                    $filename,
                );

                $companyMember->logoUrl = $filename;
            }

            $relatedAfupOffices = $data['related_afup_offices'] ?? [];
            if (!is_array($relatedAfupOffices) || array_filter($relatedAfupOffices, is_string(...)) !== $relatedAfupOffices) {
                throw new BadRequestHttpException('Invalid related AFUP offices.');
            }

            $companyMember->publicProfileEnabled = (bool) $data['enabled'];
            $companyMember->description = $this->nullableString($data['description'] ?? null);
            $companyMember->websiteUrl = $this->nullableString($data['website_url'] ?? null);
            $companyMember->contactPageUrl = $this->nullableString($data['contact_page_url'] ?? null);
            $companyMember->careersPageUrl = $this->nullableString($data['careers_page_url'] ?? null);
            $companyMember->twitterHandle = $this->nullableString($data['twitter_handle'] ?? null);
            $companyMember->setFormattedRelatedAfupOffices($relatedAfupOffices);
            $companyMember->membershipReason = $this->nullableString($data['membership_reason'] ?? null);

            $this->companyMemberRepository->save($companyMember);

            $this->addFlash('success', 'Modifications enregistrées');
            return $this->redirectToRoute('member_company_public_profile');
        }

        return $this->render(
            'site/member/company_public_profile.html.twig',
            [
                'form' => $form->createView(),
                'company_member' => $companyMember,
            ],
        );
    }

    private function nullableString(mixed $value): ?string
    {
        if (null !== $value && !is_string($value)) {
            throw new BadRequestHttpException('Expected a string.');
        }

        return $value;
    }

    private function prepareUploadedFilesDir(): string
    {
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir);
        }

        return $this->storageDir;
    }
}
