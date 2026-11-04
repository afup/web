<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Session;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Entity\Planning;
use AppBundle\Event\Entity\Repository\PlanningRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteAction extends AbstractController
{
    public function __construct(
        private readonly PlanningRepository $planningRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $id = $request->attributes->get('id');
        $planning = $this->planningRepository->find($id);
        if (!$planning instanceof Planning) {
            throw $this->createNotFoundException(sprintf('Planning not found with id "%s".', $id));
        }

        $this->planningRepository->delete($planning);

        $this->audit->log('Suppression de la programmation de la session ' . $id);
        $this->addFlash('notice', 'La programmation de la session a été supprimée');

        return $this->redirectToRoute('admin_event_sessions');
    }
}
