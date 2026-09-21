<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Entity\Repository\TicketSpecialPriceRepository;
use AppBundle\Event\Entity\TicketSpecialPrice;
use AppBundle\Event\Form\TicketSpecialPriceType;
use AppBundle\Security\Authentication;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpecialPriceAction extends AbstractController
{
    public const int EXTEND_DAYS = 3; // jours

    public function __construct(
        private readonly TicketSpecialPriceRepository $ticketSpecialPriceRepository,
        private readonly Authentication $authentication,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
        $event = $eventSelection->event;
        $eventId = $event->getId();
        if ($eventId === null) {
            throw $this->createNotFoundException();
        }

        $dateEnd = DateTimeImmutable::createFromMutable($event->getDateEndSales());

        $specialPrice = new TicketSpecialPrice();
        $specialPrice->token = base64_encode(random_bytes(30));
        $specialPrice->eventId = $eventId;
        $specialPrice->dateStart = new DateTimeImmutable();
        $specialPrice->dateEnd = $dateEnd;
        $specialPrice->createdOn = new DateTimeImmutable();
        $specialPrice->creatorId = $this->authentication->getAfupUser()->getId();

        $form = $this->createForm(TicketSpecialPriceType::class, $specialPrice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ticketSpecialPriceRepository->save($form->getData());

            $this->addFlash('notice', 'Le token a été enregistré');

            return $this->redirectToRoute('admin_event_special_price', [
                'id' => $eventId,
            ]);
        }

        return $this->render('admin/event/special_price.html.twig', [
            'special_prices' => $this->ticketSpecialPriceRepository->getByEvent($eventId),
            'event' => $event,
            'title' => 'Gestion des prix custom',
            'form' => $form->createView(),
            'event_select_form' => $eventSelection->selectForm(),
            'extend_days' => self::EXTEND_DAYS,
        ]);
    }
}
