<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Lead;

use AppBundle\Controller\Event\EventActionHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class PostLeadAction extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
    ) {}

    public function __invoke($eventSlug): Response
    {
        return $this->render('event/sponsorship_file/thanks.html.twig', [
            'event' => $this->eventActionHelper->getEvent($eventSlug),
        ]);
    }
}
