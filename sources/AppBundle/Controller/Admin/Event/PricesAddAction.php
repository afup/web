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
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

class PricesAddAction
{
    private EventActionHelper $eventActionHelper;
    private TicketTypeRepository $ticketTypeRepository;
    private TicketEventTypeRepository $ticketEventTypeRepository;
    private FormFactoryInterface $formFactory;
    private FlashBagInterface $flashBag;
    private UrlGeneratorInterface $urlGenerator;
    private Environment $twig;
    private ValidatorInterface $validator;

    public function __construct(
        EventActionHelper $eventActionHelper,
        TicketTypeRepository $ticketTypeRepository,
        TicketEventTypeRepository $ticketEventTypeRepository,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig,
        ValidatorInterface $validator
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->ticketTypeRepository = $ticketTypeRepository;
        $this->ticketEventTypeRepository = $ticketEventTypeRepository;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->validator = $validator;
    }

    public function __invoke(Request $request)
    {
        $id = $request->query->getInt('event');
        $event = $this->eventActionHelper->getEventById($id);

        $ticketEventType = new ModelTicketEventType();
        $ticketEventType->setEventId($event->getId());
        $ticketEventType->setDateStart($event->getDateStart());
        $ticketEventType->setDateEnd($event->getDateEnd());

        $ticketTypes = $this->ticketTypeRepository->getAll();
        $form = $this->formFactory->create(TicketEventType::class, $ticketEventType, [
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
                    'message' => 'Ce type de ticket existe déjà pour cet évènement.'
                ])
            ]);
            foreach ($violations as $violation) {
                $form->get('ticketType')->addError(new FormError($violation->getMessage()));
            }

            if ($form->isValid()) {
                $this->ticketEventTypeRepository->save($ticketEventType);

                $this->flashBag->add('notice', 'Le tarif a été ajouté');

                return new RedirectResponse($this->urlGenerator->generate('admin_event_prices', [
                    'id' => $event->getId()
                ]));
            }
        }

        return new Response($this->twig->render('admin/event/prices_add_edit.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'title' => 'Tarifications - Ajouter',
            'button_text' => 'Ajouter',
            'event_select_form' => $this->formFactory->create(EventSelectType::class, $event)->createView(),
        ]));
    }
}
