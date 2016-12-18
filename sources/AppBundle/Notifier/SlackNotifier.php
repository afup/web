<?php


namespace AppBundle\Notifier;


use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\Vote;
use AppBundle\Slack\Message;
use AppBundle\Slack\MessageFactory;
use JMS\Serializer\Serializer;

class SlackNotifier
{
    /**
     * @var string
     */
    private $postUrl;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * SlackNotifier constructor.
     * @param $postUrl
     * @param MessageFactory $messageFactory
     * @param Serializer $serializer
     */
    public function __construct($postUrl, MessageFactory $messageFactory, Serializer $serializer)
    {
        $this->postUrl = $postUrl;
        $this->messageFactory = $messageFactory;
        $this->serializer = $serializer;
    }

    /**
     * Send a message to slack for a new vote
     *
     * @param Vote $vote
     * @return bool
     */
    public function notifyVote(Vote $vote)
    {
        $message = $this->messageFactory->createMessageForVote($vote);
        return $this->sendMessage($message);
    }

    /**
     * Send a message to slack for a new talk
     *
     * @param Talk $talk
     * @return bool
     */
    public function notifyTalk(Talk $talk)
    {
        $message = $this->messageFactory->createMessageForTalk($talk);
        return $this->sendMessage($message);
    }

    /**
     * @param Message $message
     * @return bool
     */
    private function sendMessage(Message $message)
    {
        $ch = curl_init($this->postUrl);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['payload' => $this->serializer->serialize($message, 'json')]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return true;
    }
}
