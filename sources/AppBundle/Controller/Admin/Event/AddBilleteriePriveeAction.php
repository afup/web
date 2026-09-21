<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Entity\BilleteriePrivee;
use AppBundle\Event\Entity\Repository\BilleteriePriveeRepository;
use AppBundle\Event\Entity\Repository\TicketTypeRepository;
use AppBundle\Event\Form\BilleteriePriveeType;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddBilleteriePriveeAction extends AbstractController
{
    public function __construct(
        private readonly BilleteriePriveeRepository $billeteriePriveeRepository,
        private readonly TicketTypeRepository $ticketTypeRepository,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
        $event = $eventSelection->event;

        $billeteriePrivee = new BilleteriePrivee();
        $billeteriePrivee->token = rtrim(strtr(base64_encode(random_bytes(30)), '+/', '-_'), '=');
        $billeteriePrivee->eventId = (int) $event->getId();
        $billeteriePrivee->dateDebut = new DateTimeImmutable();
        $billeteriePrivee->dateFin = DateTimeImmutable::createFromMutable($event->getDateEndSales());
        $billeteriePrivee->createdOn = new DateTimeImmutable();

        $form = $this->createForm(BilleteriePriveeType::class, $billeteriePrivee, [
            'ticketTypes' => $this->ticketTypeRepository->findAllOrderedById(),
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

        return $this->render('admin/event/billeterie_privee_edit.html.twig', [
            'is_new' => true,
            'billeterie_privee' => $billeteriePrivee,
            'places_prises' => null,
            'event' => $event,
            'title' => 'Billeteries privées - Nouvelle',
            'edit_title' => 'Nouvelle billeterie privée',
            'button_label' => 'Créer',
            'form' => $form->createView(),
        ]);
    }
}
