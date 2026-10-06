<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Association\Form\TicketEventType;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Entity\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PricesEditAction extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly TicketTypeRepository $ticketTypeRepository,
        private readonly TicketEventTypeRepository $ticketEventTypeRepository,
        private readonly FormFactoryInterface $formFactory,
    ) {}

    public function __invoke(Request $request, int $event, int $id): Response
    {
        $event = $this->eventActionHelper->getEventById($event);
        $ticketType = $this->ticketTypeRepository->get($id);

        $ticketEventType = $this->ticketEventTypeRepository->find([
            'eventId' => $event->getId(),
            'ticketTypeId' => $ticketType->getId(),
        ]);
        if ($ticketEventType === null) {
            throw new NotFoundHttpException();
        }
        $ticketEventType->ticketType = $ticketType;

        $ticketTypes = $this->ticketTypeRepository->getAll();
        $form = $this->createForm(TicketEventType::class, $ticketEventType, [
            'ticketTypes' => $ticketTypes,
            'has_prices_defined_with_vat' => $event->hasPricesDefinedWithVat(),
        ]);
        // Pour qu'il ne soit pas modifiable dans le formulaire
        $form->remove('ticketType');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->ticketEventTypeRepository->save($ticketEventType);

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
            'event_select_form' => $this->formFactory->create(EventSelectType::class, $event, [
                'data' => $event,
            ])->createView(),
        ]);
    }
}
