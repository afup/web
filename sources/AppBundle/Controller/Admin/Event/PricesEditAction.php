<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Association\Form\TicketEventType;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PricesEditAction extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly TicketTypeRepository $ticketTypeRepository,
        private readonly TicketEventTypeRepository $ticketEventTypeRepository,
    ) {
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
        $form = $this->createForm(TicketEventType::class, $ticketEventType, [
            'ticketTypes' => $ticketTypes,
            'has_prices_defined_with_vat' => $event->hasPricesDefinedWithVat(),
        ]);
        // Pour qu'il ne soit pas modifiable dans le formulaire
        $form->remove('ticketType');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->ticketEventTypeRepository->update($ticketEventType);

            $this->addFlash('notice', 'Le tarif a été modifié');

            return $this->redirectToRoute('admin_event_prices', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('admin/event/prices_add_edit.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'title' => 'Tarifications - Modifier',
            'button_text' => 'Modifier',
            'event_select_form' => $this->createForm(EventSelectType::class, $event)->createView(),
        ]);
    }
}
