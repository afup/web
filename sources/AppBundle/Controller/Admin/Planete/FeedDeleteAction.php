<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Planete;

use Afup\Site\Logger\DbLoggerTrait;
use PlanetePHP\FeedRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FeedDeleteAction
{
    use DbLoggerTrait;

    private FeedRepository $feedRepository;
    private UrlGeneratorInterface $urlGenerator;
    private FlashBagInterface $flashBag;

    public function __construct(
        FeedRepository $feedRepository,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag
    ) {
        $this->feedRepository = $feedRepository;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $id = $request->query->get('id');
        if ($this->feedRepository->delete($id)) {
            $this->log('Suppression du flux ' . $id);
            $this->flashBag->add('notice', 'Le flux a été supprimé');
        } else {
            $this->flashBag->add('error', 'Une erreur est survenue lors de la suppression du flux');
        }

        return new RedirectResponse($this->urlGenerator->generate('admin_planete_feed_list'));
    }
}
