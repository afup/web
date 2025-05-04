<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Form\CompanyEditType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CompanyAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private CompanyMemberRepository $companyMemberRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(Request $request, ?int $id)
    {
        $company = new CompanyMember();
        if ($id) {
            $company = $this->companyMemberRepository->get($id);
            if ($company === null) {
                $this->addFlash('error', 'Personne morale non trouvée');
                return $this->redirectToRoute('admin_members_company_list');
            }
        }
        $form = $this->createForm(CompanyEditType::class, $company);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->companyMemberRepository->save($company);
                $this->addFlash('notice', 'La personne morale a été ' . ($id ? 'modifiée' : 'ajoutée'));

                return $this->redirectToRoute('admin_members_company_list', ['filter' => $company->getCompanyName()]);
            } catch (Exception) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout de la personne morale');
            }
        }

        return $this->render('admin/members/company/' . ($id ? 'edit' : 'add') . '.html.twig', [
            'form' => $form->createView(),
            'users' => $this->userRepository->search('lastname', 'asc', null, $company->getId()),
            'company' => $company,
        ]);
    }
}
