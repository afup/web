<?php

declare(strict_types=1);


namespace AppBundle\Notifier;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\Vote;
use AppBundle\Slack\Message;
use AppBundle\Slack\MessageFactory;
use JMS\Serializer\Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SlackNotifier
{
    public function __construct(
        private readonly string $postUrl,
        private readonly MessageFactory $messageFactory,
        private readonly Serializer $serializer,
        private readonly HttpClientInterface $httpClient,
    ) {
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
            'body' => [
                'payload' => $this->serializer->serialize($message, 'json'),
            ],
        ]);
    }
}
