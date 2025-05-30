<?php

declare(strict_types=1);

namespace AppBundle\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

//TODO : à supprimer quand les flash messages du legacy seront migrés
#[AsEventListener(KernelEvents::REQUEST)]
final readonly class ConvertLegacyFlashMessageKernelListener
{
    public function __construct(private RequestStack $requestStack) {}

    public function onKernelRequest(): void
    {
        try {
            $session = $this->requestStack->getSession();
        } catch (SessionNotFoundException) {
            return;
        }

        if (!$session instanceof FlashBagAwareSessionInterface) {
            return;
        }

        if (isset($_SESSION['flash']['message'])) {
            $session->getFlashBag()->add('notice', $_SESSION['flash']['message']);
        }
        if (isset($_SESSION['flash']['erreur'])) {
            $session->getFlashBag()->add('error', $_SESSION['flash']['erreur']);
        }
        unset($_SESSION['flash']);
    }
}
