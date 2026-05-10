<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ListAction extends AbstractController
{
    public function __construct(private readonly EventRepository $eventRepository) {}

    public function __invoke(): Response
    {
        $list = $this->eventRepository->getList();
        return $this->render('admin/event/list.html.twig', [
            'events' => $list,
        ]);
    }
}
