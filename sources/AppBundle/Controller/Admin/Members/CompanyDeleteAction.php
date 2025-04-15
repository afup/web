<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CompanyDeleteAction extends AbstractController
{
    use DbLoggerTrait;

    private CompanyMemberRepository $companyMemberRepository;

    public function __construct(
        CompanyMemberRepository $companyMemberRepository
    ) {
        $this->companyMemberRepository = $companyMemberRepository;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $companyMember = $this->companyMemberRepository->get($request->query->get('id'));
        if (null === $companyMember) {
            throw $this->createNotFoundException('Personne morale non trouvée');
        }
        try {
            $this->companyMemberRepository->remove($companyMember);
            $this->log('Suppression de la personne morale ' . $companyMember->getId());
            $this->addFlash('notice', 'La personne morale a été supprimée');
        } catch (InvalidArgumentException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression de la personne morale');
        }

        return $this->redirectToRoute('admin_members_company_list');
    }
}
