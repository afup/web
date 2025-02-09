<?php

declare(strict_types=1);


namespace AppBundle\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleEventSubscriber implements EventSubscriberInterface
{
    private $defaultLocale;
    public function __construct($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        if ($event->isMasterRequest() === false) {
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
