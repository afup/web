<?php

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment;

class RemoveEventAction
{
    /**
     * @var EventRepository
     */
    private $eventRepository;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(EventRepository $eventRepository, Environment $twig, CsrfTokenManagerInterface $csrfTokenManager, FlashBagInterface $flashBag, UrlGeneratorInterface $urlGenerator)
    {
        $this->eventRepository = $eventRepository;
        $this->twig = $twig;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
    }

    /**
     * @param int $id
     * @param string $token
     * @return RedirectResponse
     */
    public function __invoke($id, $token)
    {
        if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('forum_delete', $token))) {
            $this->flashBag->add('error', 'Token invalide');
            return new RedirectResponse($this->urlGenerator->generate('admin_event_list'));
        }
        $result = $this->eventRepository->getList($id);
        if (count($result) !== 1) {
            $this->flashBag->add('error', 'Identifiant d\'évènement incorrect');
            return new RedirectResponse($this->urlGenerator->generate('admin_event_list'));
        }

        if ($result->first()['est_supprimable'] !== 1) {
            $this->flashBag->add('error', 'Impossible de supprimer un évènement utilisé');
            return new RedirectResponse($this->urlGenerator->generate('admin_event_list'));
        }

        $event = $this->eventRepository->get($id);
        $this->eventRepository->delete($event);

        $this->flashBag->add('notice', 'Événement supprimé');
        return new RedirectResponse($this->urlGenerator->generate('admin_event_list'));
    }
}
