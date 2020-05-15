<?php

namespace AppBundle\Controller\Legacy;

use Afup\Site\Forum\Forum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Forum2009AgendaAction
{
    /** @var Forum */
    private $forum;
    /** @var Environment */
    private $twig;

    public function __construct(Forum $forum, Environment $twig)
    {
        $this->forum = $forum;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        return new Response($this->twig->render('legacy/forumphp2009/agenda.html.twig', [
            'agenda' => $this->forum->genAgenda(2009),
        ]));
    }
}
