<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;

class ListAction
{
    private EventRepository $eventRepository;
    private Environment $twig;
    /**
     * @var SessionInterface&Session
     */
    private SessionInterface $session;

    public function __construct(EventRepository $eventRepository, Environment $twig, SessionInterface $session)
    {
        $this->eventRepository = $eventRepository;
        $this->twig = $twig;
        $this->session = $session;
    }

    public function __invoke(Request $request): Response
    {
        //TODO : à supprimer quand les actions via le formulaire auront été migée
        if (isset($_SESSION['flash']['message'])) {
            $this->session->getFlashBag()->add('notice', $_SESSION['flash']['message']);
        }
        if (isset($_SESSION['flash']['erreur'])) {
            $this->session->getFlashBag()->add('error', $_SESSION['flash']['erreur']);
        }
        unset($_SESSION['flash']);

        $list = $this->eventRepository->getList();

        return new Response($this->twig->render('admin/event/list.html.twig', ['events'=>$list]));
    }
}
