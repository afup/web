<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Form\EventType;
use AppBundle\Accounting\Entity\Event;
use AppBundle\Accounting\Entity\Repository\EventRepository;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddEventAction extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->eventRepository->save($event);
            $this->audit->log('Ajout de l\'évènement ' . $event->name);
            $this->addFlash('notice', 'L\'évènement a été ajouté');
            return $this->redirectToRoute('admin_accounting_events_list');
        }

        return $this->render('admin/accounting/configuration/event_add.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'formTitle' => 'Ajouter un évènement',
            'submitLabel' => 'Ajouter',
        ]);
    }
}
