<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Accounting\Form\EventType;
use AppBundle\Accounting\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditEventAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {}

    public function __invoke(int $id,Request $request): Response
    {
        $event = $this->eventRepository->get($id);
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->eventRepository->save($event);
            $this->log('Modification de l\'évènement ' . $event->getName());
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
