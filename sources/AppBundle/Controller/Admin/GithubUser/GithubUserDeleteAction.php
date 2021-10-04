<?php

namespace AppBundle\Controller\Admin\GithubUser;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Event\Model\GithubUser;
use AppBundle\Event\Model\Repository\GithubUserRepository;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GithubUserDeleteAction
{
    use DbLoggerTrait;

    /** @var GithubUserRepository */
    private $githubUserRepository;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        GithubUserRepository  $githubUserRepository,
        FlashBagInterface     $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->githubUserRepository = $githubUserRepository;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request)
    {
        /** @var GithubUser|null $githubUser */
        $githubUser = $this->githubUserRepository->get($request->query->get('id'));
        if (null === $githubUser) {
            throw new NotFoundHttpException('GithubUser non trouvé');
        }
        try {
            $this->githubUserRepository->delete($githubUser);
            $this->log('Suppression de l\'utilisateur github ' . $githubUser->getId());
            $this->flashBag->add('notice', 'L\'utilisateur github a été supprimé');
        } catch (Exception $e) {
            $this->flashBag->add('error', 'Une erreur est survenue lors de la suppression de l\'utilisateur github');
        }

        return new RedirectResponse($this->urlGenerator->generate('admin_github_user_list'));
    }
}
