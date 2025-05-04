<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use AppBundle\Calendar\TechnoWatchCalendarGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TechnoWatchController extends AbstractController
{
    public function __construct(
        private readonly string $technoWatchCalendarKey,
        private readonly string $technoWatchCalendarUrl,
    ) {
    }

    public function calendar(Request $request): Response
    {
        if ($request->query->get('key') !== $this->technoWatchCalendarKey) {
            throw $this->createNotFoundException();
        }

        $generator = new TechnoWatchCalendarGenerator("Veille de l'AFUP", new \DateTime());

        $calendar = $generator->generate(
            $this->technoWatchCalendarUrl,
            $request->query->getBoolean('display_prefix', true),
            $request->query->get('filter', '')
        );

        $response = new Response($calendar);

        $response->headers->add([
            'Content-Type' => 'text/Calendar; charset=UTF-8',
            'Content-Disposition' => 'inline; filename=techno_watch_calendar.vcs',
            'Cache-Control' => 'no-cache',
            'Pragma' => 'no-cache',
        ]);

        return $response;
    }
}
