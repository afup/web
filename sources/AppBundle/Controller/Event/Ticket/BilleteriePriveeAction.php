<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Ticket;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Entity\Repository\BilleteriePriveeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;

final class BilleteriePriveeAction extends AbstractController
{
    public function __construct(
        private readonly RateLimiterFactoryInterface $billeteriePriveeLimiter,
        private readonly EventActionHelper $eventActionHelper,
        private readonly BilleteriePriveeRepository $billeteriePriveeRepository,
    ) {}

    public function __invoke(Request $request, string $eventSlug, string $token): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        /** @var Session $session */
        $session = $request->getSession();

        if ($session->has('billeterie_privee_token') === true) {
            $session->remove('billeterie_privee_token');
        }

        if ($request->isMethod(Request::METHOD_POST)) {
            $errors = [];
            if (!$this->isCsrfTokenValid('billeterie_privee', (string) $request->request->get('_csrf_token'))) {
                $errors[] = 'Jeton anti csrf invalide';
            } elseif (!$request->request->has('billeterie_privee_mot_de_passe')) {
                $errors[] = 'Mot de passe absent';
            } else {
                $limiter = $this->billeteriePriveeLimiter->create($request->getClientIp());
                $rateLimit = $limiter->consume(1);
                $billeteriePrivee = $this->billeteriePriveeRepository->findOneByToken($token);

                if (
                    !$rateLimit->isAccepted()
                    || $billeteriePrivee === null
                    || $billeteriePrivee->eventId !== $event->getId()
                    || !password_verify((string) $request->request->get('billeterie_privee_mot_de_passe'), $billeteriePrivee->motDePasse)
                ) {
                    // Même message que si le token ou le mot de passe est faux, pour ne pas révéler lequel des deux pose problème
                    $errors[] = 'Token ou mot de passe invalide.';
                    if ($rateLimit->isAccepted() === false) {
                        $errors[] = 'Trop de tentatives, merci de réessayer dans une heure.';
                    }
                } else {
                    $limiter->reset();
                    $session->set('billeterie_privee_token', $billeteriePrivee->token);

                    return $this->redirectToRoute('ticket', ['eventSlug' => $eventSlug]);
                }
            }
            $session->getFlashBag()->setAll(['error' => $errors]);
            return $this->redirectToRoute('billeterie_privee_home', ['eventSlug' => $eventSlug, 'token' => $token]);
        }

        return $this->render('event/ticket/billeterie_privee_home.html.twig', [
            'event' => $event,
            'token' => $token,
        ]);
    }
}
