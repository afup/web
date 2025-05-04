<?php

declare(strict_types=1);

namespace AppBundle\Controller\Legacy;

use Afup\Site\Forum\Forum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Forum2009AgendaAction
{
    public function __construct(
        private readonly Forum $forum,
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        return new Response($this->twig->render('legacy/forumphp2009/agenda.html.twig', [
            'agenda' => $this->forum->genAgenda(2009),
        ]));
    }
}
