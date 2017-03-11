<?php


namespace AppBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class LocaleListener
{
    private $defaultLocale;
    public function __construct($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $event)
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
}
