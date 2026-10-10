<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Email\Emails;
use AppBundle\Email\Mailer\MailUserFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EVENT_READER')]
class SendTestInscriptionEmailAction extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly Emails $emails,
    ) {}

    public function __invoke(int $id): RedirectResponse
    {
        $event = $this->eventActionHelper->getEventById($id);
        $this->emails->sendInscription($event, MailUserFactory::bureau());
        $this->addFlash('notice', 'Mail de test envoyé');

        return $this->redirectToRoute('admin_event_edit', [
            'id' => $event->getId(),
        ]);
    }
}
