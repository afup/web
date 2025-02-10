<?php

declare(strict_types=1);


namespace AppBundle\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class GithubController extends AbstractController
{
    private ClientRegistry $clientRegistry;
    public function __construct(ClientRegistry $clientRegistry)
    {
        $this->clientRegistry = $clientRegistry;
    }
    /**
     * Link to this controller to start the "connect" process
     *
     */
    public function connect()
    {
        return $this->clientRegistry
            ->getClient('github_main')
            ->redirect([], []);
    }

    /**
     * Github redirects to back here afterwards
     *
     * @return Response
     */
    public function connectCheck()
    {
        return new RedirectResponse($this->generateUrl('connection_github'));
    }
}
