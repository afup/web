<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Member;

use AppBundle\Association\Form\AdminCompanyMemberType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\User;
use AppBundle\Twig\ViewRenderer;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CompanyController extends AbstractController
{
    public function __construct(
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly ViewRenderer $view,
    ) {
    }

    public function __invoke(Request $request)
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }
        $company = $this->companyMemberRepository->get($user->getCompanyId());
        if ($company === null) {
            throw $this->createNotFoundException('Company not found');
        }

        $subscribeForm = $this->createForm(AdminCompanyMemberType::class, $company);
        $subscribeForm->handleRequest($request);

        if ($subscribeForm->isSubmitted() && $subscribeForm->isValid()) {
            /** @var CompanyMember $member */
            $member = $subscribeForm->getData();
            try {
                $this->companyMemberRepository->save($member);
                $this->addFlash('notice', 'Les modifications ont bien été enregistrées.');
            } catch (Exception) {
                $this->addFlash('error', 'Une erreur est survenue. Merci de nous contacter.');
            }

            return $this->redirectToRoute('member_company');
        }

        return $this->view->render('admin/association/membership/company.html.twig', [
            'title' => 'Mon adhésion entreprise',
            'form' => $subscribeForm->createView(),
        ]);
    }
}
