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
use Assert\Assertion;
use DateTime;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class SponsorTicketAction
{
    private EventActionHelper $eventActionHelper;
    private SponsorTicketRepository $sponsorTicketRepository;
    private SponsorTokenMail $sponsorTokenMail;
    private FlashBagInterface $flashBag;
    private Security $security;
    private UrlGeneratorInterface $urlGenerator;
    private FormFactoryInterface $formFactory;
    private Environment $twig;

    public function __construct(
        EventActionHelper $eventActionHelper,
        SponsorTicketRepository $sponsorTicketRepository,
        SponsorTokenMail $sponsorTokenMail,
        FlashBagInterface $flashBag,
        Security $security,
        UrlGeneratorInterface $urlGenerator,
        FormFactoryInterface $formFactory,
        Environment $twig
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->sponsorTicketRepository = $sponsorTicketRepository;
        $this->sponsorTokenMail = $sponsorTokenMail;
        $this->flashBag = $flashBag;
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $id = $request->query->get('id');

        $event = $this->eventActionHelper->getEventById($id);
        $tokens = $this->sponsorTicketRepository->getByEvent($event);
        $edit = $request->query->has('ticket');
        if ($edit) {
            $newToken = $this->sponsorTicketRepository->get($request->query->get('ticket'));
            $newToken->setEditedOn(new DateTime());
        } else {
            /** @var User $user */
            $user = $this->security->getUser();
            Assertion::isInstanceOf($user, User::class);
            $newToken = new SponsorTicket();
            $newToken
                ->setToken(base64_encode(random_bytes(30)))
                ->setIdForum($event->getId())
                ->setCreatedOn(new DateTime())
                ->setEditedOn(new DateTime())
                ->setCreatorId($user->getId());
        }
        $form = $this->formFactory->create(SponsorTokenType::class, $newToken);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($newToken->getId() === null) {
                $this->sponsorTokenMail->sendNotification($newToken);
            }
            $this->sponsorTicketRepository->save($newToken);
            $this->flashBag->add('notice', 'Le token a été enregistré');

            return new RedirectResponse($this->urlGenerator->generate('admin_event_sponsor_ticket',
                ['id' => $event->getId()]));
        }

        return new Response($this->twig->render('admin/event/sponsor_ticket.html.twig', [
            'tokens' => $tokens,
            'event' => $event,
            'title' => 'Gestion des inscriptions sponsors',
            'form' => $form === null ? null : $form->createView(),
            'edit' => $edit,
            'event_select_form' => $this->formFactory->create(EventSelectType::class, $event)->createView(),
        ]));
    }
}
