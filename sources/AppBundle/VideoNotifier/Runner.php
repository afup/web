<?php

namespace AppBundle\VideoNotifier;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\PlanningRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TweetRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\Tweet;
use TwitterAPIExchange;

class Runner
{
    /**
     * @var TweetGenerator
     */
    private $tweetGenerator;

    /**
     * @var PlanningRepository
     */
    private $planningRepository;

    /**
     * @var TalkRepository
     */
    private $talkRepository;

    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var SpeakerRepository
     */
    private $speakerRepository;

    /**
     * @var TweetRepository
     */
    private $tweetRepository;

    /**
     * @var TwitterAPIExchange
     */
    private $twitter;

    /**
     * @param PlanningRepository $planningRepository
     * @param TalkRepository $talkRepository
     * @param EventRepository $eventRepository
     * @param SpeakerRepository $speakerRepository
     */
    public function __construct(PlanningRepository $planningRepository, TalkRepository $talkRepository, EventRepository $eventRepository, SpeakerRepository $speakerRepository, TweetRepository $tweetRepository, TwitterAPIExchange $twitter)
    {
        $this->tweetGenerator = new TweetGenerator();
        $this->planningRepository = $planningRepository;
        $this->talkRepository = $talkRepository;
        $this->eventRepository = $eventRepository;
        $this->speakerRepository = $speakerRepository;
        $this->tweetRepository = $tweetRepository;
        $this->twitter = $twitter;
    }

    /**
     * @param Event|null $eventToFilter
     *
     * @return Tweet
     */
    public function execute(Event $eventToFilter = null)
    {
        $talkInfos = $this->getNextTalkInformations($eventToFilter);
        $tweet = $this->tweetGenerator->generate($talkInfos['talk'], $talkInfos['speakers']);
        $id = $this->sendTweet($tweet);
        return $this->saveTweet($talkInfos['talk'], $id);
    }

    /**
     * @param Event|null $eventToFilter
     *
     * @return array
     */
    private function getNextTalkInformations(Event $eventToFilter = null)
    {
        $all = $this->getAllTalkInformations($eventToFilter);
        $tweetsFromThisRound = $this->removeTweetsFromLastRound($all);
        if (0 === count($tweetsFromThisRound)) {
            throw new \LogicException("No talk found");
        }
        return $this->getRandomTalkInformations($tweetsFromThisRound);
    }

    /**
     * @param Event|null $eventToFilter
     *
     * @return array
     */
    protected function getAllTalkInformations(Event $eventToFilter = null)
    {
        $plannings = $this->planningRepository->getAll();
        $minimumEventDate = $this->getMinimumEventDate();

        $talkInformations = [];

        /** @var Planning $planning */
        foreach ($plannings as $planning) {
            $talk = $this->talkRepository->get($planning->getTalkId());
            /**
             * @var Talk $talk
             */
            if (null === $talk || !$talk->isDisplayedOnHistory() || !$talk->hasYoutubeId()) {
                continue;
            }

            if (!($talk->getType() == Talk::TYPE_FULL_LONG || $talk->getType() == Talk::TYPE_FULL_SHORT)) {
                continue;
            }

            $eventId = $planning->getEventId();

            if (null !== $eventToFilter && $eventToFilter->getId() != $eventId) {
                continue;
            }

            $event = $this->eventRepository->get($eventId);

            if (null === $event || $event->startsBefore($minimumEventDate)) {
                continue;
            }

            $speakers = $this->speakerRepository->getSpeakersByTalk($talk);

            $talkInformations[] = [
                'talk' => $talk,
                'speakers' => iterator_to_array($speakers->getIterator()),
                'count' => $this->tweetRepository->getNumberOfTweetsByTalk($talk),
            ];
        }

        return $talkInformations;
    }

    /**
     * @return \DateTime
     */
    protected function getMinimumEventDate()
    {
        $datetime = new \DateTime('now');
        $datetime->modify('-2 years');

        return $datetime;
    }

    /**
     * @param array $talksInformations
     *
     * @return string
     */
    protected function getRandomTalkInformations(array $talksInformations)
    {
        return $talksInformations[array_rand($talksInformations)];
    }

    /**
     * @param array $talksInformations
     *
     * @return array
     */
    protected function removeTweetsFromLastRound(array $talksInformations)
    {
        $maxCount = 0;
        $minCount = PHP_INT_MAX;
        foreach ($talksInformations as $talksInformation) {
            $maxCount = max($maxCount, $talksInformation['count']);
            $minCount = min($minCount, $talksInformation['count']);
        }

        if ($minCount == $maxCount) {
            return $talksInformations;
        }

        $filteredList = [];
        foreach ($talksInformations as $talksInformation) {
            if ($talksInformation['count'] == $maxCount) {
                continue;
            }
            $filteredList[] = $talksInformation;
        }

        return $filteredList;
    }

    /**
     * @param string $tweet
     *
     * @return string
     */
    private function sendTweet($tweet)
    {
        $apiResult = $this
            ->twitter
            ->buildOauth('https://api.twitter.com/1.1/statuses/update.json', 'POST')
            ->setPostfields([
                'status' => $tweet,
            ])
            ->performRequest()
        ;

        $apiResult = json_decode($apiResult, true);
        if (false === $apiResult) {
            throw new \RuntimeException(sprintf("Error reading API result : %s (%s)", var_export($apiResult, true), $tweet));
        }

        if (!isset($apiResult['id_str'])) {
            throw new \RuntimeException(sprintf("id_str not found : %s (%s)", var_export($apiResult, true), $tweet));
        }

        return $apiResult['id_str'];
    }

    /**
     * @param Talk $talk
     * @param string $tweetId
     *
     * @return Tweet
     */
    private function saveTweet(Talk $talk, $tweetId)
    {
        $tweet = new Tweet();
        $tweet->setId($tweetId);
        $tweet->setTalkId($talk->getId());
        $tweet->setCreatedAt(new \DateTime());

        $this->tweetRepository->save($tweet);

        return $tweet;
    }
}
