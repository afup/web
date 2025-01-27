<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Controller\Website\BlocksHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class LoginAction
{
    /** @var AuthenticationUtils */
    private $authenticationUtils;
    /** @var BlocksHandler */
    private $blocksHandler;
    /** @var Environment */
    private $twig;

    public function __construct(
        AuthenticationUtils $authenticationUtils,
        BlocksHandler $blocksHandler,
        Environment $twig
    ) {
        $this->authenticationUtils = $authenticationUtils;
        $this->blocksHandler = $blocksHandler;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $actualUrl = $request->getSchemeAndHttpHost() . $request->getRequestUri();
        $targetUri = $request->query->get('target', '');
        $noDomain = parse_url($targetUri, PHP_URL_HOST) === null;
        $targetPath = $targetUri !== $actualUrl && $noDomain ? $targetUri : null;

        return new Response($this->twig->render('admin/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'target_path' => $targetPath,
            'title' => 'Connexion',
            'page' => 'connexion',
            'class' => 'panel-page',
        ] + $this->blocksHandler->getDefaultBlocks()));
    }
}
