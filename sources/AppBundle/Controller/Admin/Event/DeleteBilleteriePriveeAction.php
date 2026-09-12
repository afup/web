<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Entity\BilleteriePrivee;
use AppBundle\Event\Entity\Repository\BilleteriePriveeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteBilleteriePriveeAction extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly BilleteriePriveeRepository $billeteriePriveeRepository,
    ) {}

    public function __invoke(Request $request, int $event, int $id): Response
    {
        $event = $this->eventActionHelper->getEventById($event);

        $billeteriePrivee = $this->billeteriePriveeRepository->find($id);
        if (!$billeteriePrivee instanceof BilleteriePrivee || $billeteriePrivee->eventId !== $event->getId()) {
            throw $this->createNotFoundException(sprintf('Impossible de trouver la billeterie privée ayant l\'identifiant : %s', $id));
        }

        if ($this->billeteriePriveeRepository->countPlacesPrisesParToken($billeteriePrivee->token) > 0) {
            $this->addFlash('error', 'La billeterie privée ne peut pas être supprimée car des places ont déjà été prises.');
            return $this->redirectToRoute('admin_event_billeterie_privee', ['id' => $event->getId()]);
        }

        $this->billeteriePriveeRepository->delete($billeteriePrivee);

        $this->addFlash('notice', 'La billeterie privée a été supprimée');
        return $this->redirectToRoute('admin_event_billeterie_privee', ['id' => $event->getId()]);
    }
}
