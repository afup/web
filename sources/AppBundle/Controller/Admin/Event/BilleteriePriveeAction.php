<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Entity\BilleteriePrivee;
use AppBundle\Event\Entity\Repository\BilleteriePriveeRepository;
use AppBundle\Event\Form\BilleteriePriveeType;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BilleteriePriveeAction extends AbstractController
{
    public function __construct(
        private readonly BilleteriePriveeRepository $billeteriePriveeRepository,
        private readonly TicketTypeRepository $ticketTypeRepository,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
        $event = $eventSelection->event;

        $billeteriePrivee = new BilleteriePrivee();
        $billeteriePrivee->token = base64_encode(random_bytes(30));
        $billeteriePrivee->eventId = (int) $event->getId();
        $billeteriePrivee->dateDebut = new DateTimeImmutable();
        $billeteriePrivee->dateFin = DateTimeImmutable::createFromMutable($event->getDateEndSales());
        $billeteriePrivee->createdOn = new DateTimeImmutable();

        $form = $this->createForm(BilleteriePriveeType::class, $billeteriePrivee, [
            'ticketTypes' => $this->ticketTypeRepository->getAll(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $motDePasse = $form->get('plainMotDePasse')->getData();
            if (is_string($motDePasse) && $motDePasse !== '') {
                $billeteriePrivee->motDePasse = password_hash($motDePasse, PASSWORD_DEFAULT);
            }

            $this->billeteriePriveeRepository->save($billeteriePrivee);

            $this->addFlash('notice', 'La billeterie privée a été enregistrée');

            return $this->redirectToRoute('admin_event_billeterie_privee', [
                'id' => (int) $event->getId(),
            ]);
        }

        $billeteries = [];
        foreach ($this->billeteriePriveeRepository->findByEvent((int) $event->getId()) as $billeterie) {
            $ticketType = $this->ticketTypeRepository->get($billeterie->ticketTypeId);
            $billeteries[] = [
                'billeterie_privee' => $billeterie,
                'ticket_type' => $ticketType !== null ? $ticketType->getPrettyName() : '',
                'places_prises' => $this->billeteriePriveeRepository->countPlacesPrisesParToken($billeterie->token),
            ];
        }

        return $this->render('admin/event/billeterie_privee.html.twig', [
            'billeteries_privees' => $billeteries,
            'event' => $event,
            'title' => 'Billeteries privées',
            'form' => $form->createView(),
            'event_select_form' => $eventSelection->selectForm(),
        ]);
    }
}
