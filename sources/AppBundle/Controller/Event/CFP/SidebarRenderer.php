<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\CFP;

use AppBundle\CFP\SpeakerFactory;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;
use DateTime;
use Twig_Environment;

class SidebarRenderer
{
    private TalkRepository $talkRepository;
    private SpeakerFactory $speakerFactory;
    private \Twig_Environment $twig;

    public function __construct(
        TalkRepository $talkRepository,
        SpeakerFactory $speakerFactory,
        Twig_Environment $twig
    ) {
        $this->talkRepository = $talkRepository;
        $this->speakerFactory = $speakerFactory;
        $this->twig = $twig;
    }

    /**
     * @return string
     */
    public function render(Event $event)
    {
        if ($event->getDateEndCallForPapers() < new DateTime()) {
            return '';
        }

        return $this->twig->render('event/cfp/sidebar.html.twig', [
            'talks' => $this->talkRepository->getTalksBySpeaker($event, $this->speakerFactory->getSpeaker($event)),
            'event' => $event,
        ]);
    }
}
