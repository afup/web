<?php

declare(strict_types=1);

namespace AppBundle\Controller\Auth;

use AppBundle\Association\Form\LoginType;
use AppBundle\Twig\ViewRenderer;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final readonly class LoginAction
{
    public function __construct(
        private AuthenticationUtils $authenticationUtils,
        private FormFactoryInterface $formFactory,
        private UrlGeneratorInterface $urlGenerator,
        private ViewRenderer $view,
    ) {}

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

        // Le formulaire n'est pas soumis à Symfony : c'est le firewall qui intercepte le POST sur cette même URL.
        $form = $this->formFactory->create(LoginType::class, [
            'utilisateur' => $lastUsername,
            '_target_path' => $targetPath,
        ], [
            'action' => $this->urlGenerator->generate('app_login'),
        ]);

        return $this->view->render('site/auth/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }
}
