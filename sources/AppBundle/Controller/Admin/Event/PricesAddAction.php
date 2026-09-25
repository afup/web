<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Association\Form\TicketEventType;
use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Entity\Repository\TicketEventTypeRepository;
use AppBundle\Event\Entity\TicketEventType as ModelTicketEventType;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PricesAddAction extends AbstractController
{
    public function __construct(
        private readonly TicketTypeRepository $ticketTypeRepository,
        private readonly TicketEventTypeRepository $ticketEventTypeRepository,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
        $id = $request->query->getInt('event');
        $event = $eventSelection->event;

        $ticketEventType = new ModelTicketEventType();
        $ticketEventType->eventId = (int) $event->getId();
        $ticketEventType->dateStart = $event->getDateStart() ?? new \DateTime();
        $ticketEventType->dateEnd = $event->getDateEnd() ?? new \DateTime();

        $ticketTypes = $this->ticketTypeRepository->getAll();
        $form = $this->createForm(TicketEventType::class, $ticketEventType, [
            'ticketTypes' => $ticketTypes,
            'has_prices_defined_with_vat' => $event->hasPricesDefinedWithVat(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $ticketEventType->ticketTypeId = $ticketEventType->ticketType->getId();

            // Vérification maison d'unicité : la clé primaire est composée de (ticketTypeId, eventId)
            $existant = $this->ticketEventTypeRepository->findOneBy([
                'eventId' => $ticketEventType->eventId,
                'ticketTypeId' => $ticketEventType->ticketTypeId,
            ]);
            if ($existant !== null) {
                $form->get('ticketType')->addError(new FormError('Ce type de ticket existe déjà pour cet évènement.'));
            }

            if ($form->isValid()) {
                $this->ticketEventTypeRepository->save($ticketEventType);

                $this->addFlash('notice', 'Le tarif a été ajouté');

                return $this->redirectToRoute('admin_event_prices', [
                    'id' => $event->getId(),
                ]);
            }
        }

        return $this->render('admin/event/prices_add_edit.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'title' => 'Tarifications - Ajouter',
            'button_text' => 'Ajouter',
            'event_select_form' => $eventSelection->selectForm(),
        ]);
    }
}
