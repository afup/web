<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Environment;

class ListAction
{
    private EventRepository $eventRepository;
    private Environment $twig;

    public function __construct(EventRepository $eventRepository, Environment $twig)
    {
        $this->eventRepository = $eventRepository;
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        /** @var Session $session */
        $session = $request->getSession();
        //TODO : à supprimer quand les actions via le formulaire auront été migée
        if (isset($_SESSION['flash']['message'])) {
            $session->getFlashBag()->add('notice', $_SESSION['flash']['message']);
        }
        if (isset($_SESSION['flash']['erreur'])) {
            $session->getFlashBag()->add('error', $_SESSION['flash']['erreur']);
        }
        unset($_SESSION['flash']);

        $list = $this->eventRepository->getList();

        return new Response($this->twig->render('admin/event/list.html.twig', ['events'=>$list]));
    }
}
