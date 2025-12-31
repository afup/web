<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Talk;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Form\TalkAdminType;
use AppBundle\Event\Form\TalkType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TalkToSpeakersRepository;
use AppBundle\Event\Model\Talk;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditAction extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly TalkRepository $talkRepository,
        private readonly TalkToSpeakersRepository $talkToSpeakersRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(int $id, Request $request): Response
    {
        $talk = $this->talkRepository->get($id);
        if (!$talk instanceof Talk) {
            throw $this->createNotFoundException(sprintf('Talk not found with id "%s"', $id));
        }
        $event = $this->eventRepository->get($talk->getForumId());
        if (!$event instanceof Event) {
            throw $this->createNotFoundException(sprintf('Event not found with id "%s"', $talk->getForumId()));
        }

        $form = $this->createForm(TalkAdminType::class, $talk, [
            'event' => $event,
            TalkType::OPT_COC_CHECKED => true,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->talkRepository->save($talk);
            $this->talkToSpeakersRepository->replaceSpeakers($talk, $form->get('speakers')->getData());

            $this->audit->log(sprintf('Modification de la session de %s (%d)', $talk->getTitle(), $talk->getId()));
            $this->addFlash('notice', 'La conférence a été modifiée.');

            return $this->redirectToRoute('admin_talk_list');
        }

        return $this->render('admin/talk/edit.html.twig', [
            'event' => $event,
            'form' => $form,
            'talk' => $talk,
        ]);
    }

}
