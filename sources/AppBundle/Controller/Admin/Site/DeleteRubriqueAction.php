<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Site\Model\Repository\RubriqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class DeleteRubriqueAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private RubriqueRepository $rubriqueRepository,
        private CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function __invoke(int $id, string $token): RedirectResponse
    {
        if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('rubrique_delete', $token))) {
            $this->addFlash('error', 'Token invalide');
            return $this->redirectToRoute('admin_site_rubriques_list');
        }
        $rubrique = $this->rubriqueRepository->get($id);
        $name = $rubrique->getNom();
        $this->rubriqueRepository->delete($rubrique);
        $this->log('Suppression de la Rubrique ' . $name);
        $this->addFlash('notice', 'La rubrique ' . $name . ' a été supprimée');
        return $this->redirectToRoute('admin_site_rubriques_list');
    }
}
