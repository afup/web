<?php

declare(strict_types=1);

namespace AppBundle\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[AsEventListener]
final readonly class AccessDeniedListener
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof AccessDeniedHttpException
            && !$exception instanceof AccessDeniedException
        ) {
            return;
        }

        $session = $event->getRequest()->getSession();

        if ($session instanceof FlashBagAwareSessionInterface) {
            $session->getFlashBag()->add('error', 'Vous n\'avez pas le droit d\'accéder à cette page');
        }

        $event->setResponse(new RedirectResponse($this->urlGenerator->generate('admin_home')));
    }
}
