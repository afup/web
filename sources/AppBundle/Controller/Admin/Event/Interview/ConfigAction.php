<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Interview;

use AppBundle\Event\Form\InterviewConfigType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ConfigAction extends AbstractController
{
    public function __construct(private readonly EventRepository $eventRepository) {}

    public function __invoke(Request $request, int $eventId): Response
    {
        $event = $this->eventRepository->get($eventId);
        if (!$event instanceof Event) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(InterviewConfigType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->eventRepository->save($event);
            $this->addFlash('success', 'Configuration enregistrée.');

            return $this->redirectToRoute('admin_event_interview_list', ['id' => $event->getId()]);
        }

        return $this->render('admin/event/interview/config.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }
}
