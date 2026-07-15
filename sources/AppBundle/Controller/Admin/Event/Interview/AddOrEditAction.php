<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Interview;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Entity\Interview;
use AppBundle\Event\Entity\InterviewQuestion;
use AppBundle\Event\Entity\Repository\InterviewRepository;
use AppBundle\Event\Entity\Repository\SpeakerRepository as DoctrineSpeakerRepository;
use AppBundle\Event\Entity\Speaker as DoctrineSpeaker;
use AppBundle\Event\Form\InterviewType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\SpeakerRepository as TingSpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Speaker as TingSpeaker;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Wordpress\WordpressClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

    class AddOrEditAction extends AbstractController
    {
    public function __construct(
        private readonly TingSpeakerRepository $tingSpeakerRepository,
        private readonly DoctrineSpeakerRepository $doctrineSpeakerRepository,
        private readonly InterviewRepository $interviewRepository,
        private readonly TalkRepository $talkRepository,
        private readonly WordpressClient $wordpressClient,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection, ?int $interviewId = null): Response
    {
        $event = $eventSelection->event;

        if ($interviewId !== null) {
            $interview = $this->interviewRepository->find($interviewId);
            if ($interview === null) {
                throw $this->createNotFoundException('Interview introuvable.');
            }
        } else {
            $interview = new Interview();
            $interview->eventId = (int) $event->getId();
            $interview->addQuestion(new InterviewQuestion());
        }

        $form = $this->createForm(InterviewType::class, $interview, [
            'available_speakers' => $this->getAvailableSpeakers($event, $interview),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->interviewRepository->save($interview);

            $savedToWordpress = $this->saveToWordpress($interview, $event);

            if ($savedToWordpress) {
                $this->addFlash('success', "L'interview a été enregistrée en base et sur WordPress.");
            } else {
                $this->addFlash('notice', "L'interview a été enregistrée en base uniquement. L'enregistrement sur WordPress n'a pas fonctionné.");
            }

            return $this->redirectToRoute('admin_event_interview_list', ['id' => $event->getId()]);
        }

        return $this->render('admin/event/interview/edit.html.twig', [
            'event' => $event,
            'interview' => $interview,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return array<string, DoctrineSpeaker>
     */
    private function getAvailableSpeakers(Event $event, Interview $interview): array
    {
        $scheduledSpeakerIds = [];
        foreach ($this->tingSpeakerRepository->getScheduledSpeakersByEvent($event, true) as $row) {
            $speaker = $row['speaker'] ?? null;
            if ($speaker instanceof TingSpeaker) {
                $scheduledSpeakerIds[] = (int) $speaker->getId();
            }
        }

        $existingInterviews = $this->interviewRepository->findIndexedBySpeakerIds($scheduledSpeakerIds);

        $available = [];
        foreach ($scheduledSpeakerIds as $id) {
            if (!isset($existingInterviews[$id]) || in_array($id, $interview->getSpeakerIds(), true)) {
                $available[] = $this->doctrineSpeakerRepository->find($id);
            }
        }

        $available = array_filter($available);
        usort($available, fn(DoctrineSpeaker $a, DoctrineSpeaker $b) => strcmp($a->label, $b->label));

        $choices = [];
        /** @var DoctrineSpeaker $speaker */
        foreach ($available as $speaker) {
            $choices[$speaker->label] = $speaker;
        }

        return $choices;
    }

    private function saveToWordpress(Interview $interview, Event $event): bool
    {
        $plannedTalks = [];
        foreach ($interview->speakers as $speaker) {
            foreach ($this->talkRepository->getTalksBySpeaker($event, $speaker->id) as $talk) {
                foreach ($this->talkRepository->getByTalkWithSpeakers($talk) as $row) {
                    if ($row['talk'] instanceof Talk) {
                        $plannedTalks[] = $row['talk'];
                    }
                }
            }
        }

        try {
            $wordpressPostId = $this->wordpressClient->persistInterview(
                $interview,
                $event,
                $interview->speakers->toArray(),
                $plannedTalks,
            );

            $interview->wordpressPostId = $wordpressPostId;
            $this->interviewRepository->save($interview);

            return true;
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur WordPress : ' . $e->getMessage());
            return false;
        }
    }
}
