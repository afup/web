<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Session;

use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Repository\PlanningRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalendarAjaxAction extends AbstractController
{
    public function __construct(private readonly PlanningRepository $planningRepository) {}

    public function __invoke(int $id, Request $request): Response
    {
        $planning = $this->planningRepository->get($id);
        if (!$planning) {
            throw $this->createNotFoundException('Planning not found: ' . $id);
        }
        $data = $request->toArray();

        // Le calendrier envoie l'heure telle qu'affichée, sans décalage :
        // c'est donc l'heure locale de l'événement, pas celle du navigateur.
        $timezone = new \DateTimeZone(Planning::TIMEZONE);

        $planning->setStart(new \DateTime($data['start'], $timezone));
        $planning->setEnd(new \DateTime($data['end'], $timezone));
        $planning->setRoomId((int) $data['roomId']);

        $this->planningRepository->save($planning);

        return new Response();
    }
}
