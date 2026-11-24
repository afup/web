<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Interview;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Entity\Interview;
use AppBundle\Event\Entity\InterviewQuestion;
use AppBundle\Event\Entity\Repository\InterviewRepository;
use AppBundle\Event\Entity\Repository\SpeakerRepository;
use AppBundle\Event\Entity\Speaker;
use AppBundle\Event\Form\InterviewType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Wordpress\WordpressClient;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EVENT_WRITER')]
class AddOrEditAction extends AbstractController
{
    public function __construct(
        private readonly SpeakerRepository $speakerRepository,
        private readonly InterviewRepository $interviewRepository,
        private readonly TalkRepository $talkRepository,
        private readonly WordpressClient $wordpressClient,
        private readonly LoggerInterface $logger,
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
     * @return array<string, Speaker>
     */
    private function getAvailableSpeakers(Event $event, Interview $interview): array
    {
        $scheduledSpeakers = $this->speakerRepository->getScheduledSpeakers((int) $event->getId(), true);
        $existingInterviews = $this->interviewRepository->findIndexedBySpeakerIds(array_map(static fn(Speaker $speaker): int => $speaker->id, $scheduledSpeakers));

        $available = array_values(array_filter(
            $scheduledSpeakers,
            static fn(Speaker $speaker): bool => !isset($existingInterviews[$speaker->id]) || in_array($speaker->id, $interview->getSpeakerIds(), true),
        ));
        usort($available, fn(Speaker $a, Speaker $b): int => strcmp($a->label, $b->label));

        $choices = [];
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
                $row = $this->talkRepository->getPublishedScheduledWithEvent($talk);
                if ($row !== null) {
                    $plannedTalks[] = $row['talk'];
                }
            }
        }

        try {
            $persistedInterview = $this->wordpressClient->persistInterview(
                $interview,
                $event,
                $interview->speakers->toArray(),
                $plannedTalks,
            );

            $interview->wordpressPostId = $persistedInterview->id;
            $interview->wordpressPostSlug = $persistedInterview->slug;
            $this->interviewRepository->save($interview);

            return true;
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur WordPress : ' . $e->getMessage());
            $this->logger->error('Erreur WordPress : ' . $e->getMessage());

            return false;
        }
    }
}
