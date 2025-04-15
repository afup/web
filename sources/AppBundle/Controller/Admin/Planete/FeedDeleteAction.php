<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Planete;

use Afup\Site\Logger\DbLoggerTrait;
use PlanetePHP\FeedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class FeedDeleteAction extends AbstractController
{
    use DbLoggerTrait;

    private FeedRepository $feedRepository;

    public function __construct(FeedRepository $feedRepository)
    {
        $this->feedRepository = $feedRepository;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $id = $request->query->get('id');
        if ($this->feedRepository->delete($id)) {
            $this->log('Suppression du flux ' . $id);
            $this->addFlash('notice', 'Le flux a été supprimé');
        } else {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression du flux');
        }

        return $this->redirectToRoute('admin_planete_feed_list');
    }
}
