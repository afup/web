<?php

declare(strict_types=1);


namespace AppBundle\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GithubController extends AbstractController
{
    private ClientRegistry $clientRegistry;
    public function __construct(ClientRegistry $clientRegistry)
    {
        $this->clientRegistry = $clientRegistry;
    }
    /**
     * Link to this controller to start the "connect" process
     */
    public function connect(): RedirectResponse
    {
        return $this->clientRegistry
            ->getClient('github_main')
            ->redirect([], []);
    }

    /**
     * GitHub redirects to back here afterwards
     */
    public function connectCheck(): RedirectResponse
    {
        return new RedirectResponse($this->generateUrl('connection_github'));
    }
}
