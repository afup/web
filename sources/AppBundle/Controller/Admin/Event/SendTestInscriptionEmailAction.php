<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Email\Emails;
use AppBundle\Email\Mailer\MailUserFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SendTestInscriptionEmailAction
{
    private EventActionHelper $eventActionHelper;
    private Emails $emails;
    private UrlGeneratorInterface $urlGenerator;
    private FlashBagInterface $flashBag;

    public function __construct(
        EventActionHelper $eventActionHelper,
        Emails $emails,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->emails = $emails;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
    }

    public function __invoke(int $id): RedirectResponse
    {
        $event = $this->eventActionHelper->getEventById($id);
        $this->emails->sendInscription($event, MailUserFactory::bureau());
        $this->flashBag->add('notice', 'Mail de test envoyé');

        return new RedirectResponse($this->urlGenerator->generate('admin_event_edit', [
            'id' => $event->getId()
        ]));
    }
}
