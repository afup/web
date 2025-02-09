<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Site\Model\Repository\RubriqueRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class DeleteRubriqueAction
{
    use DbLoggerTrait;

    private CsrfTokenManagerInterface $csrfTokenManager;

    private FlashBagInterface $flashBag;

    private UrlGeneratorInterface $urlGenerator;

    private RubriqueRepository $rubriqueRepository;

    public function __construct(
        RubriqueRepository $rubriqueRepository,
        CsrfTokenManagerInterface $csrfTokenManager,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag
    ) {
        $this->rubriqueRepository =  $rubriqueRepository;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @param int $id
     * @param string $token
     */
    public function __invoke($id, $token): RedirectResponse
    {
        if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('rubrique_delete', $token))) {
            $this->flashBag->add('error', 'Token invalide');
            return new RedirectResponse($this->urlGenerator->generate('admin_site_rubriques_list'));
        }
        $rubrique = $this->rubriqueRepository->get($id);
        $name = $rubrique->getNom();
        $this->rubriqueRepository->delete($rubrique);
        $this->log('Suppression de la Rubrique ' . $name);
        $this->flashBag->add('notice', 'La rubrique ' . $name . ' a été supprimée');
        return new RedirectResponse($this->urlGenerator->generate('admin_site_rubriques_list'));
    }
}
