<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin;

use AppBundle\Twig\ViewRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginAction
{
    public function __construct(
        private readonly AuthenticationUtils $authenticationUtils,
        private readonly ViewRenderer $view,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $actualUrl = $request->getSchemeAndHttpHost() . $request->getRequestUri();
        $targetUri = $request->query->get('target', '');
        $noDomain = parse_url($targetUri, PHP_URL_HOST) === null;
        $targetPath = $targetUri !== $actualUrl && $noDomain ? $targetUri : null;

        return $this->view->render('admin/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'target_path' => $targetPath,
            'title' => 'Connexion',
            'page' => 'connexion',
            'class' => 'panel-page',
        ]);
    }
}
