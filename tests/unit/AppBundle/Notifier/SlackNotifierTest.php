<?php

declare(strict_types=1);

namespace AppBundle\Tests\Notifier;

use AppBundle\Notifier\SlackNotifier;
use AppBundle\Slack\Attachment;
use AppBundle\Slack\Field;
use AppBundle\Slack\Message;
use AppBundle\Slack\MessageFactory;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Translation\Translator;

final class SlackNotifierTest extends TestCase
{
    public function testSendMessage(): void
    {
        $mockResponse = new MockResponse();
        $client = new MockHttpClient([$mockResponse]);

        $messageFactory = new MessageFactory(new Translator('fr'));

        $notifier = new SlackNotifier(
            'http://fake-slack-endpoint',
            $messageFactory,
            SerializerBuilder::create()->build(),
            $client,
        );

        $message = new Message();
        $message->setChannel("m1");
        $message->setText("m2");
        $message->setUsername("m3");
        $message->setIconUrl("m4");
        $message->addAttachment(
            (new Attachment())
                ->setText("a1")
                ->setTitle("a2")
                ->setColor("a3")
                ->setAuthorIcon("a4")
                ->setAuthorLink("a5")
                ->setAuthorName("a6")
                ->setFallback("a7")
                ->setMrkdwnIn(["a8"])
                ->setPretext("a9")
                ->setTitleLink("a10")
                ->addField(
                    (new Field())
                        ->setTitle("f1")
                        ->setValue("f2")
                        ->setShort(true)
                )
        );

        $notifier->sendMessage($message);

        self::assertEquals(
            'payload=%7B%22channel%22%3A%22m1%22%2C%22username%22%3A%22m3%22%2C%22text%22%3A%22m2%22%2C%22icon_url%22%3A%22m4%22%2C%22attachments%22%3A%5B%7B%22fallback%22%3A%22a7%22%2C%22pretext%22%3A%22a9%22%2C%22author_name%22%3A%22a6%22%2C%22author_link%22%3A%22a5%22%2C%22author_icon%22%3A%22a4%22%2C%22title%22%3A%22a2%22%2C%22title_link%22%3A%22a10%22%2C%22text%22%3A%22a1%22%2C%22color%22%3A%22a3%22%2C%22fields%22%3A%5B%7B%22title%22%3A%22f1%22%2C%22value%22%3A%22f2%22%2C%22short%22%3Atrue%7D%5D%2C%22mrkdwn_in%22%3A%5B%22a8%22%5D%7D%5D%7D',
            $mockResponse->getRequestOptions()['body'],
        );
    }
}
