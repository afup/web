<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\AuditLog\Audit;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CompanyDeleteAction extends AbstractController
{
    public function __construct(
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $companyMember = $this->companyMemberRepository->get($request->query->get('id'));
        if (null === $companyMember) {
            throw $this->createNotFoundException('Personne morale non trouvée');
        }
        try {
            $this->companyMemberRepository->remove($companyMember);
            $this->audit->log('Suppression de la personne morale ' . $companyMember->getId());
            $this->addFlash('notice', 'La personne morale a été supprimée');
        } catch (InvalidArgumentException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (Exception) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression de la personne morale');
        }

        return $this->redirectToRoute('admin_members_company_list');
    }
}
