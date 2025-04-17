<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Form\RoomType;
use AppBundle\Event\Model\Repository\RoomRepository;
use AppBundle\Event\Model\Room;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class RoomAction extends AbstractController
{
    private EventActionHelper $eventActionHelper;
    private FormFactoryInterface $formFactory;
    private RoomRepository $roomRepository;

    public function __construct(
        EventActionHelper $eventActionHelper,
        FormFactoryInterface $formFactory,
        RoomRepository $roomRepository
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->formFactory = $formFactory;
        $this->roomRepository = $roomRepository;
    }

    public function __invoke(Request $request)
    {
        $id = $request->query->get('id');

        $event = $this->eventActionHelper->getEventById($id);
        $rooms = $this->roomRepository->getByEvent($event);
        $editForms = $this->getFormsForRooms($rooms);

        foreach ($editForms as $form) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $room = $form->getData();
                if ($request->request->has('delete')) {
                    $this->roomRepository->delete($room);
                    $this->addFlash('notice', sprintf('La salle "%s" a été supprimée.', $room->getName()));
                } else {
                    $this->roomRepository->save($room);
                    $this->addFlash('notice', sprintf('La salle "%s" a été sauvegardée.', $room->getName()));
                }

                return $this->redirectToRoute('admin_event_room', [
                    'id' => $event->getId()
                ]);
            }
        }

        $newRoom = new Room();
        $newRoom->setEventId($event->getId());

        $addForm = $this->createForm(RoomType::class, $newRoom);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $newRoom = $addForm->getData();
            $this->roomRepository->save($newRoom);
            $this->addFlash('notice', sprintf('La salle "%s" a été ajoutée.', $newRoom->getName()));

            return $this->redirectToRoute('admin_event_room', [
                'id' => $event->getId()
            ]);
        }

        return $this->render('admin/event/rooms.html.twig', [
            'event' => $event,
            'rooms' => $rooms,
            'addForm' => $addForm === null ? null : $addForm->createView(),
            'editForms' => $editForms === null ? null : array_map(static fn (Form $form) => $form->createView(), $editForms),
            'title' => 'Gestion des salles',
            'event_select_form' => $this->createForm(EventSelectType::class, $event)->createView(),
        ]);
    }

    /**
     * @return FormInterface[]
     */
    private function getFormsForRooms(CollectionInterface $rooms): array
    {
        $forms = [];
        foreach ($rooms as $room) {
            $forms[] = $this->formFactory->createNamedBuilder('edit_room_' . $room->getId(), RoomType::class,
                $room)->getForm();
        }

        return $forms;
    }
}
