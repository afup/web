<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Event;

use AppBundle\Calendar\JsonPlanningGenerator;
use AppBundle\Controller\Event\EventActionHelper;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class PlanningJsonAction
{
    public function __construct(
        private EventActionHelper $eventActionHelper,
        private JsonPlanningGenerator $jsonPlanningGenerator,
    ) {}

    public function __invoke(string $eventSlug): JsonResponse
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        return new JsonResponse($this->jsonPlanningGenerator->generate($event));
    }
}
