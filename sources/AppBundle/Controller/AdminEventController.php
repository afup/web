<?php


namespace AppBundle\Controller;

use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Form\RoomType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\RoomRepository;
use AppBundle\Event\Model\Room;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class AdminEventController extends Controller
{
    public function roomAction(Request $request)
    {
        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        $event = null;
        if ($request->query->has('id') === false) {
            $event = $eventRepository->getNextEvent();
            $event = $eventRepository->get($event->getId());
        } else {
            $id = $request->query->getInt('id');
            $event = $eventRepository->get($id);
        }

        if ($event === null) {
            return $this->createNotFoundException('Could not find event');
        }

        /**
         * @var $roomRepository RoomRepository
         */
        $roomRepository = $this->get('ting')->get(RoomRepository::class);
        $rooms = $roomRepository->getByEvent($event);
        $editForms = $this->getFormsForRooms($rooms);

        foreach ($editForms as $form) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $room = $form->getData();
                if ($request->request->has('delete')) {
                    $roomRepository->delete($room);
                    $this->addFlash('notice', sprintf('La salle "%s" a été supprimée.', $room->getName()));
                } else {
                    $roomRepository->save($room);
                    $this->addFlash('notice', sprintf('La salle "%s" a été sauvegardée.', $room->getName()));
                }
                return $this->redirectToRoute('admin_event_room', ['id' => $event->getId()]);
            }
        }

        $newRoom = new Room();
        $newRoom->setEventId($event->getId());

        $addForm = $this->createForm(RoomType::class, $newRoom);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $newRoom = $addForm->getData();
            $roomRepository->save($newRoom);
            $this->addFlash('notice', sprintf('La salle "%s" a été ajoutée.', $newRoom->getName()));
            return $this->redirectToRoute('admin_event_room', ['id' => $event->getId()]);
        }

        return $this->render(':admin/event:rooms.html.twig',
            [
                'event' => $event,
                'rooms' => $rooms,
                'addForm' => $addForm->createView(),
                'editForms' => array_map(function (Form $form) {
                    return $form->createView();
                }, $editForms),
                'title' => 'Gestion des salles'
            ]
        );
    }

    public function changeEventAction(Event $selectedEvent = null)
    {
        $form = $this->createForm(
            EventSelectType::class,
            $selectedEvent,
            ['event_repository' => $this->get('ting')->get(EventRepository::class)]
        );
        return $this->render(':admin/event:change_event.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param CollectionInterface $rooms
     * @return Form[]
     */
    private function getFormsForRooms(CollectionInterface $rooms)
    {
        $forms = [];
        foreach ($rooms as $room) {
            $forms[] = $this->get('form.factory')->createNamedBuilder('edit_room_' . $room->getId(), RoomType::class, $room)->getForm();
        }
        return $forms;
    }
}
