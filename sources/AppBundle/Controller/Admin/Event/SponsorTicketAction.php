<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Association\Model\User;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Form\SponsorTokenType;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\SponsorTicket;
use AppBundle\Event\Ticket\SponsorTokenMail;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SponsorTicketAction extends AbstractController
{
    private EventActionHelper $eventActionHelper;
    private SponsorTicketRepository $sponsorTicketRepository;
    private SponsorTokenMail $sponsorTokenMail;

    public function __construct(
        EventActionHelper $eventActionHelper,
        SponsorTicketRepository $sponsorTicketRepository,
        SponsorTokenMail $sponsorTokenMail
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->sponsorTicketRepository = $sponsorTicketRepository;
        $this->sponsorTokenMail = $sponsorTokenMail;
    }

    public function __invoke(Request $request): Response
    {
        $id = $request->query->get('id');

        $event = $this->eventActionHelper->getEventById($id);
        $tokens = $this->sponsorTicketRepository->getByEvent($event);
        $edit = $request->query->has('ticket');
        if ($edit) {
            $newToken = $this->sponsorTicketRepository->get($request->query->get('ticket'));
            $newToken->setEditedOn(new DateTime());
        } else {
            $user = $this->getUser();
            if (!$user instanceof User) {
                throw $this->createAccessDeniedException();
            }
            $newToken = new SponsorTicket();
            $newToken
                ->setToken(base64_encode(random_bytes(30)))
                ->setIdForum($event->getId())
                ->setCreatedOn(new DateTime())
                ->setEditedOn(new DateTime())
                ->setCreatorId($user->getId());
        }
        $form = $this->createForm(SponsorTokenType::class, $newToken);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($newToken->getId() === null) {
                $this->sponsorTokenMail->sendNotification($newToken);
            }
            $this->sponsorTicketRepository->save($newToken);
            $this->addFlash('notice', 'Le token a été enregistré');

            return $this->redirectToRoute('admin_event_sponsor_ticket', [
                'id' => $event->getId()
            ]);
        }

        return $this->render('admin/event/sponsor_ticket.html.twig', [
            'tokens' => $tokens,
            'event' => $event,
            'title' => 'Gestion des inscriptions sponsors',
            'form' => $form === null ? null : $form->createView(),
            'edit' => $edit,
            'event_select_form' => $this->createForm(EventSelectType::class, $event)->createView(),
        ]);
    }
}
