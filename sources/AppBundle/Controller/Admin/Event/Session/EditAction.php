<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Session;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Entity\Planning;
use AppBundle\Event\Entity\Repository\PlanningRepository;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\RoomRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Room;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditAction extends AbstractController
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly PlanningRepository $planningRepository,
        private readonly EventRepository $eventRepository,
        private readonly RoomRepository $roomRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $talk = $this->talkRepository->get($request->attributes->get('talkId'));
        if (!$talk) {
            throw $this->createNotFoundException('Talk not found');
        }
        $event = $this->eventRepository->get($talk->getForumId());
        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }
        $roomChoices = $this->roomChoices($event);

        if ($request->attributes->get('sessionId')) {
            $planning = $this->planningRepository->find($request->attributes->get('sessionId'));
            if (!$planning instanceof Planning) {
                throw $this->createNotFoundException('Planning not found');
            }
        } else {
            $planning = new Planning();
            $planning->talkId = $talk->getId();
            $planning->eventId = $event->getId();
            $planning->start = $this->firstDayOfEvent($event);
            $planning->end = $this->firstDayOfEvent($event);
        }

        $form = $this->getForm($planning, $roomChoices);

        if ($request->query->get('mode') === 'add') {
            $planning->start?->setTime(9, 0);
            $planning->end?->setTime(9, 40);
            $planning->roomId = array_first($roomChoices);

            $this->planningRepository->save($planning);

            return $this->redirectToRoute('admin_event_sessions');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $isNew = $planning->id === null;

            $this->planningRepository->save($planning);

            if ($isNew) {
                $log = 'Ajout du planning de la session de ' . $talk->getTitle();
                $this->addFlash('notice', 'Le planning de la session a été créé');
            } else {
                $log = 'Modification du planning de la session de ' . $talk->getTitle() . ' (' . $talk->getId() . ')';
                $this->addFlash('notice', 'Le planning de la session a été modifié');
            }

            $this->audit->log($log);

            return $this->redirectToRoute('admin_event_sessions');
        }

        return $this->render('event/session/edit.html.twig', [
            'form' => $form,
            'talk' => $talk,
            'event' => $event,
        ]);
    }

    private function getForm(Planning $data, array $roomChoices): FormInterface
    {
        return $this->createFormBuilder($data)
            ->add('start', DateTimeType::class, [
                'label' => 'Début',
                'view_timezone' => Planning::TIMEZONE,
            ])
            ->add('end', DateTimeType::class, [
                'label' => 'Fin',
                'view_timezone' => Planning::TIMEZONE,
            ])
            ->add('roomId', ChoiceType::class, [
                'label' => 'Salle',
                'choices' => $roomChoices,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Soumettre',
                'attr' => ['class' => 'ui primary button'],
            ])
            ->getForm();

    }

    /**
     * Minuit le premier jour de l'événement, dans la timezone où le planning est saisi.
     */
    private function firstDayOfEvent(Event $event): \DateTime
    {
        return new \DateTime(
            $event->getDateStart()?->format('Y-m-d') ?? 'today',
            new \DateTimeZone(Planning::TIMEZONE),
        );
    }

    /**
     * @return array<string, int>
     */
    private function roomChoices(Event $event): array
    {
        $roomChoices = [];

        $rooms = $this->roomRepository->getByEvent($event);
        /** @var Room $room */
        foreach ($rooms as $room) {
            $roomChoices[$room->getName()] = $room->getId();
        }

        return $roomChoices;
    }
}
