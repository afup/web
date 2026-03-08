<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Entity\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListEventAction
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        $events = $this->eventRepository->getAllSortedByName();

        return new Response($this->twig->render('admin/accounting/configuration/event_list.html.twig', [
            'events' => $events,
        ]));
    }
}
