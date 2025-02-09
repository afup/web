<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Ticket\SponsorTokenMail;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResendSponsorTokenAction
{
    private EventActionHelper $eventActionHelper;
    private SponsorTicketRepository $sponsorTicketRepository;
    private SponsorTokenMail $sponsorTokenMail;
    private UrlGeneratorInterface $urlGenerator;
    private FlashBagInterface $flashBag;

    public function __construct(
        EventActionHelper $eventActionHelper,
        SponsorTicketRepository $sponsorTicketRepository,
        SponsorTokenMail $sponsorTokenMail,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->sponsorTicketRepository = $sponsorTicketRepository;
        $this->sponsorTokenMail = $sponsorTokenMail;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $event = $this->eventActionHelper->getEventById($request->query->get('id'));
        $token = $this->sponsorTicketRepository->get($request->request->get('sponsor_token_id'));
        if ($token === null) {
            throw new NotFoundHttpException(sprintf('Could not find token with id: %s', $request->request->get('sponsor_token_id')));
        }
        $this->sponsorTokenMail->sendNotification($token);
        $this->flashBag->add('notice', 'Le mail a été renvoyé');

        return new RedirectResponse($this->urlGenerator->generate('admin_event_sponsor_ticket', ['id' => $event->getId()]));
    }
}
