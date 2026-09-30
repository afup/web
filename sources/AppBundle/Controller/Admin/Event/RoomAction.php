<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\AdminEventSelection;
use Symfony\Component\Form\FormView;
use AppBundle\Event\Entity\Repository\RoomRepository;
use AppBundle\Event\Entity\Room;
use AppBundle\Event\Form\RoomType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RoomAction extends AbstractController
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly RoomRepository $roomRepository,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
        $event = $eventSelection->event;
        $rooms = $this->roomRepository->getByEvent($event);
        $editForms = $this->getFormsForRooms($rooms);

        foreach ($editForms as $i => $form) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $room = $form->getData();
                if ($request->request->has('delete')) {
                    $this->roomRepository->delete($room);
                    $this->addFlash('notice', sprintf('La salle "%s" a été supprimée.', $room->name));
                } else {
                    $this->roomRepository->save($room);
                    $this->addFlash('notice', sprintf('La salle "%s" a été sauvegardée.', $room->name));
                }

                return $this->redirectToRoute('admin_event_room', [
                    'id' => $event->getId(),
                ]);
            }
        }

        $newRoom = new Room();
        $newRoom->eventId = $event->getId();

        $addForm = $this->createForm(RoomType::class, $newRoom);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $this->roomRepository->save($newRoom);
            $this->addFlash('notice', sprintf('La salle "%s" a été ajoutée.', $newRoom->name));

            return $this->redirectToRoute('admin_event_room', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('admin/event/rooms.html.twig', [
            'event' => $event,
            'rooms' => $rooms,
            'addForm' => $addForm->createView(),
            'editForms' => array_map(static fn(FormInterface $form): FormView => $form->createView(), $editForms),
            'title' => 'Gestion des salles',
            'event_select_form' => $eventSelection->selectForm(),
        ]);
    }

    /**
     * @param array<Room> $rooms
     *
     * @return FormInterface[]
     */
    private function getFormsForRooms(array $rooms): array
    {
        $forms = [];
        foreach ($rooms as $room) {
            $forms[] = $this->formFactory->createNamedBuilder('edit_room_' . $room->id, RoomType::class,
                $room)->getForm();
        }

        return $forms;
    }
}
