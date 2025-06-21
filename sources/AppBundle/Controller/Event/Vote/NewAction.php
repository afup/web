<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Vote;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Model\GithubUser;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\VoteRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\Vote;
use AppBundle\Notifier\SlackNotifier;
use CCMBenchmark\Ting\Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

final class NewAction extends VoteController
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly SlackNotifier $slackNotifier,
        private readonly EventActionHelper $eventActionHelper,
        private readonly TalkRepository $talkRepository,
        private readonly VoteRepository $voteRepository,
    ) {}

    public function __invoke(Request $request, string $eventSlug, int $talkId): JsonResponse
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);
        if (!$event->isVoteAvailable()) {
            return new JsonResponse(['errors' => ['Cfp is closed !']], Response::HTTP_BAD_REQUEST);
        }

        $vote = new Vote();
        if ($this->getUser() instanceof GithubUser) {
            $vote->setUser($this->getUser()->getId());
        }

        $form = $this->createVoteForm($eventSlug, $talkId, $vote);
        $form->handleRequest($request);

        if ($form->isSubmitted() === false) {
            return new JsonResponse(['errors' => ['Form not submitted']], Response::HTTP_BAD_REQUEST);
        }

        if ($form->isValid() === false) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $talk = $this->findTalk($talkId);
        if (!$talk) {
            return new JsonResponse(['errors' => ['Talk does not exists']], Response::HTTP_BAD_REQUEST);
        }

        $vote = $form->getData();
        $vote->setSubmittedOn(new \DateTime());

        try {
            $vote->setTalk($talk);
            $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use ($vote): void {
                $this->slackNotifier->notifyVote($vote);
            });
            $this->voteRepository->upsert($vote);
        } catch (Exception $e) {
            return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['errors' => []]);
    }

    private function findTalk(int $talkId): Talk|false
    {
        $talk = $this->talkRepository->getOneBy(['id' => $talkId]);
        if ($talk) {
            return $talk;
        }

        return false;
    }
}
