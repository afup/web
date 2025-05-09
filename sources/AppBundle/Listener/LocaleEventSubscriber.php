<?php

declare(strict_types=1);


namespace AppBundle\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private $defaultLocale)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
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
    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 100]];
    }
}
