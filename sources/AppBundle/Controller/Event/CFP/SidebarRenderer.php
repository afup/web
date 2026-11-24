<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\CFP;

use AppBundle\CFP\SpeakerFactory;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;
use Twig\Environment;

class SidebarRenderer
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly SpeakerFactory $speakerFactory,
        private readonly Environment $twig,
    ) {}

    public function render(Event $event): string
    {
        if (!$event->isCfpOpen()) {
            return '';
        }

        $speaker = $this->speakerFactory->getSpeaker($event);

        return $this->twig->render('event/cfp/sidebar.html.twig', [
            'talks' => isset($speaker->id) ? $this->talkRepository->getTalksBySpeaker($event, $speaker->id) : [],
            'event' => $event,
        ]);
    }
}
