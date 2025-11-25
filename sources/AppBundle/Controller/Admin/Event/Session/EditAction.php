<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Session;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\PlanningRepository;
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
        $talk = $this->talkRepository->get($request->get('talkId'));
        if (!$talk) {
            throw $this->createNotFoundException('Talk not found');
        }
        $event = $this->eventRepository->get($talk->getForumId());
        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }
        $roomChoices = $this->roomChoices($event);

        if ($request->get('sessionId')) {
            $planning = $this->planningRepository->get($request->get('sessionId'));
        } else {
            $planning = new Planning();
            $planning->setTalkId($talk->getId());
            $planning->setEventId($event->getId());
            $planning->setStart(clone $event->getDateStart());
            $planning->setEnd(clone $event->getDateStart());
        }


        $form = $this->getForm($planning, $roomChoices);

        if ($request->get('mode') === 'add') {
            $planning->getStart()?->setTime(9, 0);
            $planning->getEnd()?->setTime(9, 40);
            $planning->setRoomId($roomChoices[array_key_first($roomChoices)]);

            $this->planningRepository->save($planning);

            return $this->redirectToRoute('admin_event_sessions');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $isNew = !$planning->getId();

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
            ])
            ->add('end', DateTimeType::class, [
                'label' => 'Fin',
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
