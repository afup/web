<?php

declare(strict_types=1);

namespace AppBundle\Listener;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener(event: 'kernel.request', priority: 100)]
final readonly class LocaleEventSubscriber
{
    public function __construct(
        #[Autowire('%kernel.default_locale%')]
        private string $defaultLocale,
    ) {}

    public function __invoke(RequestEvent $event): void
    {
        if ($event->isMainRequest() === false) {
            return ;
        }

        $locale = $this->defaultLocale;
        if ($event->getRequest()->hasSession() && $event->getRequest()->getSession()->has('_locale')) {
            $locale = $event->getRequest()->getSession()->get('_locale');
        }

        if ($event->getRequest()->query->has('_locale')) {
            $locale = $event->getRequest()->query->get('_locale');
            if ($event->getRequest()->hasSession()) {
                $event->getRequest()->getSession()->set('_locale', $locale);
            }
        }
        $event->getRequest()->setLocale($locale);
    }
}
