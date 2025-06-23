<?php

declare(strict_types=1);

namespace AppBundle\Controller\Github;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * GitHub redirects to back here afterwards
 */
final class ConnectCheckAction extends AbstractController
{
    public function __invoke(): RedirectResponse
    {
        return new RedirectResponse($this->generateUrl('connection_github'));
    }
}
