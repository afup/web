<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Talk;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Form\TalkAdminType;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TalkToSpeakersRepository;
use AppBundle\Event\Model\Talk;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddAction extends AbstractController
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly TalkToSpeakersRepository $talkToSpeakersRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
        $talk = new Talk();
        $talk->setForumId($eventSelection->event->getId());

        $form = $this->createForm(TalkAdminType::class, $talk, [
            'event' => $eventSelection->event,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->talkRepository->save($talk);
            $this->talkToSpeakersRepository->replaceSpeakers($talk, $form->get('speakers')->getData());

            $this->audit->log(sprintf('Ajout de la session de %s', $talk->getTitle()));
            $this->addFlash('notice', 'La conférence a été ajoutée.');

            return $this->redirectToRoute('admin_talk_list');
        }

        return $this->render('admin/talk/add.html.twig', [
            'event' => $eventSelection->event,
            'form' => $form,
        ]);
    }

}
