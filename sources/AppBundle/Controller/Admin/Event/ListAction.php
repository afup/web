<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListAction extends AbstractController
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function __invoke(Request $request): Response
    {
        //TODO : à supprimer quand les actions via le formulaire auront été migée
        if (isset($_SESSION['flash']['message'])) {
            $this->addFlash('notice', $_SESSION['flash']['message']);
        }
        if (isset($_SESSION['flash']['erreur'])) {
            $this->addFlash('error', $_SESSION['flash']['erreur']);
        }
        unset($_SESSION['flash']);

        $list = $this->eventRepository->getList();

        return $this->render('admin/event/list.html.twig', [
            'events' => $list
        ]);
    }
}
