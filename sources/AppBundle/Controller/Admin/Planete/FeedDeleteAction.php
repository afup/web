<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Planete;

use AppBundle\AuditLog\Audit;
use PlanetePHP\FeedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class FeedDeleteAction extends AbstractController
{
    public function __construct(
        private readonly FeedRepository $feedRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $id = $request->query->get('id');
        if ($this->feedRepository->delete($id)) {
            $this->audit->log('Suppression du flux ' . $id);
            $this->addFlash('notice', 'Le flux a été supprimé');
        } else {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression du flux');
        }

        return $this->redirectToRoute('admin_planete_feed_list');
    }
}
