<?php

namespace AppBundle\Controller;

use AppBundle\Calendar\TechnoWatchCalendarGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TechnoWatchController extends SiteBaseController
{
    public function calendarAction(Request $request)
    {
        if ($request->query->get('key') != $this->getParameter('techno_watch_calendar_key')) {
            throw $this->createNotFoundException();
        }

        $generator = new TechnoWatchCalendarGenerator("Veille de l'AFUP", new \DateTime());

        $calendar = $generator->generate(
            $this->getParameter('techno_watch_calendar_url'),
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
