<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\CFP;

use AppBundle\CFP\PhotoStorage;
use AppBundle\CFP\SpeakerFactory;
use AppBundle\CFP\ViewModel\EventTalkList;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexAction extends AbstractController
{
    const MAX_EVENTS_HISTORY = 50;
    private TalkRepository $talkRepository;
    private SpeakerFactory $speakerFactory;
    private PhotoStorage $photoStorage;
    private SidebarRenderer $sidebarRenderer;
    private EventActionHelper $eventActionHelper;
    private EventRepository $eventRepository;

    public function __construct(
        EventActionHelper $eventActionHelper,
        EventRepository $eventRepository,
        TalkRepository $talkRepository,
        SpeakerFactory $speakerFactory,
        PhotoStorage $photoStorage,
        SidebarRenderer $sidebarRenderer
    ) {
        $this->talkRepository = $talkRepository;
        $this->speakerFactory = $speakerFactory;
        $this->photoStorage = $photoStorage;
        $this->sidebarRenderer = $sidebarRenderer;
        $this->eventActionHelper = $eventActionHelper;
        $this->eventRepository = $eventRepository;
    }

    public function __invoke(Request $request): Response
    {
        $event = $this->eventActionHelper->getEvent($request->attributes->get('eventSlug'));
        $now = new DateTime();
        if ($event->getDateEndCallForPapers() < $now && $event->getDateEndVote() > $now) {
            return $this->redirectToRoute('event_index');
        }
        $speaker = $this->speakerFactory->getSpeaker($event);
        $eventTalkList = new EventTalkList($event);
        foreach ($this->talkRepository->getTalksBySpeaker($event, $speaker) as $talk) {
            $eventTalkList->addTalk($talk);
        }
        /** @var EventTalkList[] $previousEventTalkLists */
        $previousEventTalkLists = [];
        /** @var Event $previousEvent */
        foreach ($this->eventRepository->getPreviousEvents(self::MAX_EVENTS_HISTORY) as $previousEvent) {
            $previousEventTalkLists[$previousEvent->getId()] = new EventTalkList($previousEvent);
        }
        /** @var Talk $talk */
        foreach ($this->talkRepository->getPreviousTalksBySpeaker($event, $speaker) as $talk) {
            if (array_key_exists($talk->getForumId(), $previousEventTalkLists)) {
                $previousEventTalkLists[$talk->getForumId()]->addTalk($talk);
            }
        }
        // Remove events with no talks submitted
        $previousEventTalkLists = array_filter($previousEventTalkLists, static fn (EventTalkList $previousEventTalkList): bool => [] !== $previousEventTalkList->getTalks());

        return $this->render('event/cfp/home.html.twig', [
            'event' => $event,
            'eventTalkList' => $eventTalkList,
            'previousEventTalkLists' => $previousEventTalkLists,
            'speaker' => $speaker,
            'speakerPhoto' => $this->photoStorage->getUrl($speaker),
            'sidebar' => $this->sidebarRenderer->render($event),
        ]);
    }
}
