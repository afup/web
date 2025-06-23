<?php

declare(strict_types=1);

namespace AppBundle\Controller\Github;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Link to this controller to start the "connect" process
 */
final readonly class ConnectAction
{
    public function __construct(private ClientRegistry $clientRegistry) {}

    public function __invoke(): RedirectResponse
    {
        return $this->clientRegistry
            ->getClient('github_main')
            ->redirect([], []);
    }
}
