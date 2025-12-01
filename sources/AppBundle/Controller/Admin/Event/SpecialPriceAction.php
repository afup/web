<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\Support\EventSelectFactory;
use AppBundle\Event\Form\TicketSpecialPriceType;
use AppBundle\Event\Model\Repository\TicketSpecialPriceRepository;
use AppBundle\Event\Model\TicketSpecialPrice;
use AppBundle\Security\Authentication;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpecialPriceAction extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly TicketSpecialPriceRepository $ticketSpecialPriceRepository,
        private readonly EventSelectFactory $eventSelectFactory,
        private readonly Authentication $authentication,
    ) {}

    public function __invoke(Request $request): Response
    {
        $id = $request->query->get('id');

        $event = $this->eventActionHelper->getEventById($id);

        $specialPrice = new TicketSpecialPrice();
        $specialPrice
            ->setToken(base64_encode(random_bytes(30)))
            ->setEventId($event->getId())
            ->setDateStart(new DateTime())
            ->setDateEnd($event->getDateEndSales())
            ->setCreatedOn(new DateTime())
            ->setCreatorId($this->authentication->getAfupUser()->getId());

        $form = $this->createForm(TicketSpecialPriceType::class, $specialPrice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ticketSpecialPriceRepository->save($form->getData());

            $this->addFlash('notice', 'Le token a été enregistré');

            return $this->redirectToRoute('admin_event_special_price', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('admin/event/special_price.html.twig', [
            'special_prices' => $this->ticketSpecialPriceRepository->getByEvent($event),
            'event' => $event,
            'title' => 'Gestion des prix custom',
            'form' => $form->createView(),
            'event_select_form' => $this->eventSelectFactory->create($event, $request)->createView(),
        ]);
    }
}
