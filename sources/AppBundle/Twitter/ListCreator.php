<?php

namespace AppBundle\Twitter;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Speaker;
use TwitterAPIExchange;

class ListCreator
{
    /**
     * @var TwitterAPIExchange
     */
    private $twitterAPIExchange;

    /**
     * @var SpeakerRepository
     */
    private $speakerRepository;

    /**
     * @param TwitterAPIExchange $twitterAPIExchange
     * @param SpeakerRepository $speakerRepository
     */
    public function __construct(TwitterAPIExchange $twitterAPIExchange, SpeakerRepository $speakerRepository)
    {
        $this->twitterAPIExchange = $twitterAPIExchange;
        $this->speakerRepository = $speakerRepository;
    }

    /**
     * @param Event $event
     * @param null $customName
     */
    public function create(Event $event, $customName = null)
    {
        $listJson = $this->twitterAPIExchange->request(
            'https://api.twitter.com/1.1/lists/create.json',
            'POST',
            [
                'name' => null !== $customName ? $customName : $event->getTitle(),
            ]
        );

        $list = json_decode($listJson, true);

        if (false === $list) {
            throw new \RuntimeException('Erreur à la lecture des informations de la liste');
        }

        if (isset($list['errors'])) {
            throw new \RuntimeException('Erreur à la création de la liste : ' . $listJson);
        }

        $this->twitterAPIExchange->request(
            'https://api.twitter.com/1.1/lists/members/create_all.json',
            'POST',
            [
                'list_id' => $list['id_str'],
                'screen_name' => implode(',', $this->getTwitterHandles($event))
            ]
        );
    }

    /**
     * @param Event $event
     *
     * @return array
     */
    protected function getTwitterHandles(Event $event)
    {
        $twitterHandles = [];
        /** @var Speaker $speaker */
        foreach ($this->speakerRepository->getSpeakersByEvent($event) as $speaker) {
            $twitter = $speaker->getCleanedTwitter();
            if (null === $twitter) {
                continue;
            }
            $twitterHandles[] = $twitter;
        }

        return $twitterHandles;
    }
}
