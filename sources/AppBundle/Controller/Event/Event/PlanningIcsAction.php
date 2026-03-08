<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Event;

use AppBundle\Calendar\IcsPlanningGenerator;
use AppBundle\Controller\Event\EventActionHelper;
use Symfony\Component\HttpFoundation\Response;

final readonly class PlanningIcsAction
{
    public function __construct(
        private EventActionHelper $eventActionHelper,
        private IcsPlanningGenerator $icsPlanningGenerator,
    ) {}

    public function __invoke(string $eventSlug): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        $response = new Response($this->icsPlanningGenerator->generateForEvent($event));

        $response->headers->add([
            'Content-Type' => 'text/Calendar; charset=UTF-8',
            'Content-Disposition' => sprintf('inline; filename=planning_%s.vcs', $event->getPath()),
            'Cache-Control' => 'no-cache',
            'Pragma' => 'no-cache',
        ]);

        return $response;
    }
}
