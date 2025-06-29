<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Ticket;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Security\ActionThrottling\ActionThrottling;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

final class SponsorTicketAction extends AbstractController
{
    public function __construct(
        private readonly ActionThrottling $actionThrottling,
        private readonly EventActionHelper $eventActionHelper,
        private readonly SponsorTicketRepository $sponsorTicketRepository,
    ) {}

    public function __invoke(Request $request, $eventSlug): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        /** @var Session $session */
        $session = $request->getSession();

        if ($session->has('sponsor_ticket_id') === true) {
            $session->remove('sponsor_ticket_id');
        }

        if ($request->isMethod(Request::METHOD_POST)) {
            $errors = [];
            if (!$this->isCsrfTokenValid('sponsor_ticket', $request->request->get('_csrf_token'))) {
                $errors[] = 'Jeton anti csrf invalide';
            } elseif ($request->request->has('sponsor_token') === false) {
                $errors[] = 'Token absent';
            } else {
                $token = $request->request->get('sponsor_token');
                $sponsorTicket = $this->sponsorTicketRepository->getOneBy(['token' => $token]);
                if (
                    $this->actionThrottling->isActionBlocked('sponsor_token', $request->getClientIp())
                    ||
                    $sponsorTicket === null
                ) {
                    // Si l'IP a fait trop de tentatives, on affiche le meme message que si le token n'existe pas
                    // L'ip est bloquée pendant un temps mais il ne faut pas en informer celui qui tente - pour éviter
                    // qu'il ne change d'IP
                    $errors[] = 'Ce token n\'existe pas.';
                    $this->actionThrottling->log('sponsor_token', $request->getClientIp());
                } else {
                    $session->set('sponsor_ticket_id', $sponsorTicket->getId());
                    $this->actionThrottling->clearLogsForIp('sponsor_token', $request->getClientIp());

                    return $this->redirectToRoute('sponsor_ticket_form', ['eventSlug' => $eventSlug]);
                }
            }
            $session->getFlashBag()->setAll(['error' => $errors]);
            return $this->redirectToRoute('sponsor_ticket_home', ['eventSlug' => $eventSlug]);
        }

        return $this->render('event/ticket/sponsor_home.html.twig', ['event' => $event]);
    }
}
