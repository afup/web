<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ListAction extends AbstractController
{
    public function __construct(private readonly EventRepository $eventRepository)
    {
    }

    public function __invoke(): Response
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
            'events' => $list,
        ]);
    }
}
