<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\SuperApero;

use AppBundle\AuditLog\Audit;
use AppBundle\SuperApero\Entity\Repository\SuperAperoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class DeleteAction extends AbstractController
{
    public function __construct(
        private readonly SuperAperoRepository $superAperoRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(int $id, Request $request): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('super_apero_delete', $request->request->getString('_token'))) {
            $this->addFlash('error', 'Token invalide');

            return $this->redirectToRoute('admin_super_apero_list');
        }

        $superApero = $this->superAperoRepository->find($id);

        if ($superApero === null) {
            throw $this->createNotFoundException('Super apéro non trouvé');
        }

        $annee = $superApero->annee();
        $this->superAperoRepository->delete($superApero);
        $this->audit->log('Suppression du Super Apéro ' . $annee);
        $this->addFlash('notice', 'Le Super Apéro ' . $annee . ' a été supprimé');

        return $this->redirectToRoute('admin_super_apero_list');
    }
}
