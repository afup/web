<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Email\Emails;
use AppBundle\Email\Mailer\MailUserFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SendTestInscriptionEmailAction extends AbstractController
{
    private EventActionHelper $eventActionHelper;
    private Emails $emails;

    public function __construct(
        EventActionHelper $eventActionHelper,
        Emails $emails
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->emails = $emails;
    }

    public function __invoke(int $id): RedirectResponse
    {
        $event = $this->eventActionHelper->getEventById($id);
        $this->emails->sendInscription($event, MailUserFactory::bureau());
        $this->addFlash('notice', 'Mail de test envoyé');

        return $this->redirectToRoute('admin_event_edit', [
            'id' => $event->getId()
        ]);
    }
}
