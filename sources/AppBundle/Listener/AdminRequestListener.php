<?php

namespace AppBundle\Listener;


use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminRequestListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (substr($event->getRequest()->getPathInfo(), 0, 6) !== '/admin') {
            return;
        }
        if ($event->getRequest()->server->has('AFUP_CONTEXT') === false || $event->getRequest()->server->get('AFUP_CONTEXT') !== true) {
            #throw new AccessDeniedException();
        }
    }
}
