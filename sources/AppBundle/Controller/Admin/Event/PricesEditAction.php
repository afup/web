<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Association\Form\TicketEventType;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class PricesEditAction
{
    private EventActionHelper $eventActionHelper;
    private TicketTypeRepository $ticketTypeRepository;
    private TicketEventTypeRepository $ticketEventTypeRepository;
    private FormFactoryInterface $formFactory;
    private FlashBagInterface $flashBag;
    private UrlGeneratorInterface $urlGenerator;
    private Environment $twig;

    public function __construct(
        EventActionHelper $eventActionHelper,
        TicketTypeRepository $ticketTypeRepository,
        TicketEventTypeRepository $ticketEventTypeRepository,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->ticketTypeRepository = $ticketTypeRepository;
        $this->ticketEventTypeRepository = $ticketEventTypeRepository;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
    }

    public function __invoke(Request $request, $event, $id)
    {
        $event = $this->eventActionHelper->getEventById($event);
        $ticketType = $this->ticketTypeRepository->get($id);

        $ticketEventType = $this->ticketEventTypeRepository->get([
            'id_event' => $event->getId(),
            'id_tarif' => $ticketType->getId(),
        ]);
        $ticketEventType->setTicketType($ticketType);

        $ticketTypes = $this->ticketTypeRepository->getAll();
        $form = $this->formFactory->create(TicketEventType::class, $ticketEventType, [
            'ticketTypes' => $ticketTypes,
            'has_prices_defined_with_vat' => $event->hasPricesDefinedWithVat(),
        ]);
        // Pour qu'il ne soit pas modifiable dans le formulaire
        $form->remove('ticketType');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->ticketEventTypeRepository->update($ticketEventType);

            $this->flashBag->add('notice', 'Le tarif a été modifié');

            return new RedirectResponse($this->urlGenerator->generate('admin_event_prices', [
                'id' => $event->getId()
            ]));
        }

        return new Response($this->twig->render('admin/event/prices_add_edit.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'title' => 'Tarifications - Modifier',
            'button_text' => 'Modifier',
            'event_select_form' => $this->formFactory->create(EventSelectType::class, $event)->createView(),
        ]));
    }
}
