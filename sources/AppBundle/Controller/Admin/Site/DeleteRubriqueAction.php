<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Site\Model\Repository\RubriqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class DeleteRubriqueAction extends AbstractController
{
    use DbLoggerTrait;

    private CsrfTokenManagerInterface $csrfTokenManager;

    private RubriqueRepository $rubriqueRepository;

    public function __construct(
        RubriqueRepository $rubriqueRepository,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->rubriqueRepository =  $rubriqueRepository;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function __invoke(int $id, string $token, Request $request): RedirectResponse
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
