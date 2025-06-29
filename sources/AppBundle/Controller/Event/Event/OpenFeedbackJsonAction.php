<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Openfeedback\OpenfeedbackJsonGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class OpenFeedbackJsonAction
{
    public function __construct(
        private EventActionHelper $eventActionHelper,
        private OpenfeedbackJsonGenerator $openfeedbackJsonGenerator,
    ) {}

    public function __invoke(string $eventSlug): JsonResponse
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        $response =  new JsonResponse($this->openfeedbackJsonGenerator->generate($event));

        $response->headers->set('Access-Control-Allow-Origin', 'https://openfeedback.io');

        return $response;
    }
}
