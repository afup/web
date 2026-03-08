<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Talk;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteAction extends AbstractController
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(int $id, Request $request): Response
    {
        $talk = $this->talkRepository->get($id);
        if (!$talk instanceof Talk) {
            throw $this->createNotFoundException(sprintf('Talk not found with id "%s"', $id));
        }
        $this->talkRepository->delete($talk);

        $this->audit->log(sprintf('Suppression de la session de %s (%d)', $talk->getTitle(), $talk->getId()));
        $this->addFlash('notice', 'La conférence a été supprimée.');

        return $this->redirectToRoute('admin_talk_list');
    }

}
