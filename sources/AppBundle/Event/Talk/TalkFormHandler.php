<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TalkToSpeakersRepository;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use AppBundle\Notifier\SlackNotifier;
use CCMBenchmark\Ting\UnitOfWork;
use DateTime;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;

class TalkFormHandler
{
    private TalkRepository $talkRepository;
    private SpeakerRepository $speakerRepository;
    private SlackNotifier $slackNotifier;
    private EventDispatcherInterface $eventDispatcher;
    private TalkToSpeakersRepository $talkToSpeakersRepository;
    private UnitOfWork $unitOfWork;

    public function __construct(
        TalkRepository $talkRepository,
        SpeakerRepository $speakerRepository,
        SlackNotifier $slackNotifier,
        EventDispatcherInterface $eventDispatcher,
        TalkToSpeakersRepository $talkToSpeakersRepository,
        UnitOfWork $unitOfWork
    ) {
        $this->talkRepository = $talkRepository;
        $this->speakerRepository = $speakerRepository;
        $this->slackNotifier = $slackNotifier;
        $this->eventDispatcher = $eventDispatcher;
        $this->talkToSpeakersRepository = $talkToSpeakersRepository;
        $this->unitOfWork = $unitOfWork;
    }

    public function handle(Request $request, Event $event, FormInterface $form, Speaker $speaker): bool
    {
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            return false;
        }
        /** @var Talk $talk */
        $talk = $form->getData();
        $talk->setSubmittedOn(new DateTime());
        $this->speakerRepository->save($speaker);
        if (!$this->unitOfWork->isManaged($talk)) {
            $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use ($talk, $event): void {
                $this->slackNotifier->notifyTalk($talk, $event);
            });
        }
        $this->talkRepository->save($talk);
        $this->talkToSpeakersRepository->addSpeakerToTalk($talk, $speaker);

        return true;
    }
}
