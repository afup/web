<?php

declare(strict_types=1);

namespace AppBundle\Controller\Legacy;

use Afup\Site\Forum\AppelConferencier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class Forum2016SessionsAction
{
    private AppelConferencier $appelConferencier;
    private TranslatorInterface $translator;
    private Environment $twig;

    public function __construct(
        AppelConferencier $appelConferencier,
        TranslatorInterface $translator,
        Environment $twig
    ) {
        $this->appelConferencier = $appelConferencier;
        $this->translator = $translator;
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        $eventId = 15;
        $sessions = $this->appelConferencier->obtenirListeSessionsPlannifies($eventId);
        $day1key = $this->translator->trans('jeudi 27 octobre 2016');
        $day2key = $this->translator->trans('vendredi 28 octobre 2016');
        $journees = [
            $day1key => [],
            $day2key => [],
        ];
        foreach ($sessions as $session) {
            $session['conferenciers'] = $this->appelConferencier->obtenirConferenciersPourSession($session['session_id']);
            $session['journees'] = explode(' ', $session['journee']);

            if ('27' === date('d', $session['debut'])) {
                $journees[$day1key][] = $session;
            } else {
                $journees[$day2key][] = $session;
            }
        }

        return new Response($this->twig->render('legacy/forumphp2016/sessions.html.twig', [
            'journees' => $journees,
        ]));
    }
}
