<?php

namespace AppBundle\Controller\Admin\Site\Feuilles;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Site\Model\Repository\FeuilleRepository;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class DeleteFeuilleAction
{
    use DbLoggerTrait;

    /**
    * @var CsrfTokenManagerInterface
    */
    private $csrfTokenManager;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var Environment */
    private $twig;

    /** @var FeuilleRepository */
    private $feuilleRepository;

    public function __construct(
        FeuilleRepository $feuilleRepository,
        CsrfTokenManagerInterface $csrfTokenManager,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag
    ) {
        $this->feuilleRepository =  $feuilleRepository;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @param int $id
     * @param string $token
     * @return RedirectResponse
     */
    public function __invoke($id, $token)
    {
        if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('feuille_delete', $token))) {
            $this->flashBag->add('error', 'Token invalide');
            return new RedirectResponse($this->urlGenerator->generate('admin_site_feuilles_list'));
        }
        $feuille = $this->feuilleRepository->get($id);
        $name = $feuille->getNom();
        try {
            $this->feuilleRepository->delete($feuille);
            $this->log('Suppression de la Feuille ' . $name);
            $this->flashBag->add('notice', 'La feuille ' . $name . ' a été supprimée');
        } catch (Exception $e) {
            $this->flashBag->add('error', 'Une erreur est survenue lors de la suppression de la feuille' . $name);
        }
        return new RedirectResponse($this->urlGenerator->generate('admin_site_feuilles_list'));
    }
}
