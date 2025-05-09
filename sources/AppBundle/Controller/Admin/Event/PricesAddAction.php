<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Association\Form\TicketEventType;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use AppBundle\Event\Model\TicketEventType as ModelTicketEventType;
use AppBundle\Validator\Constraints\UniqueEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PricesAddAction extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly TicketTypeRepository $ticketTypeRepository,
        private readonly TicketEventTypeRepository $ticketEventTypeRepository,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $id = $request->query->getInt('event');
        $event = $this->eventActionHelper->getEventById($id);

        $ticketEventType = new ModelTicketEventType();
        $ticketEventType->setEventId($event->getId());
        $ticketEventType->setDateStart($event->getDateStart());
        $ticketEventType->setDateEnd($event->getDateEnd());

        $ticketTypes = $this->ticketTypeRepository->getAll();
        $form = $this->createForm(TicketEventType::class, $ticketEventType, [
            'ticketTypes' => $ticketTypes,
            'has_prices_defined_with_vat' => $event->hasPricesDefinedWithVat(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $ticketEventType->setTicketTypeId($ticketEventType->getTicketType()->getId());

            $violations = $this->validator->validate($ticketEventType, [
                new UniqueEntity([
                    'fields' => ['ticketTypeId', 'eventId'],
                    'repository' => $this->ticketEventTypeRepository,
                    'message' => 'Ce type de ticket existe déjà pour cet évènement.',
                ]),
            ]);
            foreach ($violations as $violation) {
                $form->get('ticketType')->addError(new FormError($violation->getMessage()));
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
            'event_select_form' => $this->createForm(EventSelectType::class, $event)->createView(),
        ]);
    }
}
