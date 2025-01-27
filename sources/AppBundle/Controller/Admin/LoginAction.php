<?php

namespace AppBundle\Controller\Admin;

use AppBundle\WebsiteBlocks;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginAction
{
    /** @var AuthenticationUtils */
    private $authenticationUtils;
    private WebsiteBlocks $websiteBlocks;

    public function __construct(
        AuthenticationUtils $authenticationUtils,
        WebsiteBlocks $websiteBlocks
    ) {
        $this->authenticationUtils = $authenticationUtils;
        $this->websiteBlocks = $websiteBlocks;
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

        return $this->websiteBlocks->render('admin/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'target_path' => $targetPath,
            'title' => 'Connexion',
            'page' => 'connexion',
            'class' => 'panel-page',
        ]);
    }
}
