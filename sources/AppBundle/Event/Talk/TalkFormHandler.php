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
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;

class TalkFormHandler
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly SpeakerRepository $speakerRepository,
        private readonly SlackNotifier $slackNotifier,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly TalkToSpeakersRepository $talkToSpeakersRepository,
        private readonly UnitOfWork $unitOfWork,
        private readonly TalkSubmissionConfirmationMail $confirmationMail,
        private readonly LoggerInterface $logger,
    ) {}

    public function handle(Request $request, Event $event, FormInterface $form, Speaker $speaker): bool
    {
        $form->handleRequest($request);
        if (!$event->isCfpOpen() || !$form->isSubmitted() || !$form->isValid()) {
            return false;
        }
        /** @var Talk $talk */
        $talk = $form->getData();
        $talk->setSubmittedOn(new DateTime());
        $this->speakerRepository->save($speaker);
        $locale = $request->getLocale();
        if (!$this->unitOfWork->isManaged($talk)) {
            $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use ($talk, $event): void {
                try {
                    $this->slackNotifier->notifyTalk($talk, $event);
                } catch (\Throwable $e) {
                    $this->logger->warning('Slack talk notification failed: ' . $e->getMessage());
                }
            });
            $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use ($talk, $event, $speaker, $locale): void {
                try {
                    $this->confirmationMail->send($talk, $event, $speaker, $locale);
                } catch (\Throwable $e) {
                    $this->logger->error('CFP confirmation mail failed: ' . $e->getMessage(), [
                        'exception' => $e,
                        'talkId' => $talk->getId(),
                    ]);
                }
            });
        }
        $this->talkRepository->save($talk);
        $this->talkToSpeakersRepository->addSpeakerToTalk($talk, $speaker);

        return true;
    }
}
