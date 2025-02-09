<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Form\RoomType;
use AppBundle\Event\Model\Repository\RoomRepository;
use AppBundle\Event\Model\Room;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class RoomAction
{
    private EventActionHelper $eventActionHelper;
    private FormFactoryInterface $formFactory;
    private RoomRepository $roomRepository;
    private FlashBagInterface $flashBag;
    private UrlGeneratorInterface $urlGenerator;
    private Environment $twig;

    public function __construct(
        EventActionHelper $eventActionHelper,
        RoomRepository $roomRepository,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->roomRepository = $roomRepository;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
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
                    $this->flashBag->add('notice', sprintf('La salle "%s" a été supprimée.', $room->getName()));
                } else {
                    $this->roomRepository->save($room);
                    $this->flashBag->add('notice', sprintf('La salle "%s" a été sauvegardée.', $room->getName()));
                }

                return new RedirectResponse($this->urlGenerator->generate('admin_event_room',
                    ['id' => $event->getId()]));
            }
        }

        $newRoom = new Room();
        $newRoom->setEventId($event->getId());

        $addForm = $this->formFactory->create(RoomType::class, $newRoom);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $newRoom = $addForm->getData();
            $this->roomRepository->save($newRoom);
            $this->flashBag->add('notice', sprintf('La salle "%s" a été ajoutée.', $newRoom->getName()));

            return new RedirectResponse($this->urlGenerator->generate('admin_event_room',
                ['id' => $event->getId()]));
        }

        return new Response($this->twig->render('admin/event/rooms.html.twig', [
            'event' => $event,
            'rooms' => $rooms,
            'addForm' => $addForm === null ? null : $addForm->createView(),
            'editForms' => $editForms === null ? null : array_map(static fn (Form $form) => $form->createView(), $editForms),
            'title' => 'Gestion des salles',
            'event_select_form' => $this->formFactory->create(EventSelectType::class, $event)->createView(),
        ]));
    }

    /**
     * @return Form[]
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
