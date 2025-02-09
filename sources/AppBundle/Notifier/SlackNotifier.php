<?php

declare(strict_types=1);


namespace AppBundle\Notifier;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\Vote;
use AppBundle\Slack\Message;
use AppBundle\Slack\MessageFactory;
use GuzzleHttp\ClientInterface;
use JMS\Serializer\Serializer;

class SlackNotifier
{
    private string $postUrl;
    private MessageFactory $messageFactory;
    private Serializer $serializer;
    private ClientInterface $httpClient;

    public function __construct(string $postUrl, MessageFactory $messageFactory, Serializer $serializer, ClientInterface $httpClient)
    {
        $this->postUrl = $postUrl;
        $this->messageFactory = $messageFactory;
        $this->serializer = $serializer;
        $this->httpClient = $httpClient;
    }

    public function notifyVote(Vote $vote): void
    {
        $this->sendMessage($this->messageFactory->createMessageForVote($vote));
    }

    public function notifyTalk(Talk $talk, Event $event): void
    {
        $this->sendMessage($this->messageFactory->createMessageForTalk($talk, $event));
    }

    public function sendMessage(Message $message): void
    {
        $this->httpClient->request('POST', $this->postUrl, [
            'form_params' => [
                'payload' => $this->serializer->serialize($message, 'json'),
            ],
        ]);
    }
}
