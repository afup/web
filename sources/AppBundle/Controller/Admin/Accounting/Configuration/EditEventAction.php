<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Form\EventType;
use AppBundle\Accounting\Model\Repository\EventRepository;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditEventAction extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(int $id,Request $request): Response
    {
        $event = $this->eventRepository->get($id);
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->eventRepository->save($event);
            $this->audit->log('Modification de l\'évènement ' . $event->getName());
            $this->addFlash('notice', 'L\'évènement a été modifié');
            return $this->redirectToRoute('admin_accounting_events_list');
        }

        return $this->render('admin/accounting/configuration/event_edit.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'formTitle' => 'Modifier un évènement',
            'submitLabel' => 'Modifier',
        ]);
    }
}
