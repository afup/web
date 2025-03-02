<?php

declare(strict_types=1);

namespace AppBundle\Notifier\tests\units;

use AppBundle\Notifier\SlackNotifier as TestedSlackNotifier;
use AppBundle\Slack\Attachment;
use AppBundle\Slack\Field;
use AppBundle\Slack\Message;
use AppBundle\Slack\MessageFactory;
use atoum;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\SerializerBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

class SlackNotifier extends atoum
{
    public function testSendMessage(): void
    {
        $container = [];

        $mock = new MockHandler([
            new Response(200),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($container));

        $client = new Client([
            'handler' => $handlerStack,
        ]);


        /** @var TranslatorInterface $translatorMock */
        $translatorMock = $this->newMockInstance(TranslatorInterface::class);

        $messageFactory = new MessageFactory($translatorMock);

        $notifier = new TestedSlackNotifier(
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

        /** @var Request $request */
        $request = $container[0]['request'] ?? null;

        $this
            ->object($request)
            ->isInstanceOf(Request::class)
            ->string($request->getBody()->getContents())
            ->isEqualTo('payload=%7B%22channel%22%3A%22m1%22%2C%22username%22%3A%22m3%22%2C%22text%22%3A%22m2%22%2C%22icon_url%22%3A%22m4%22%2C%22attachments%22%3A%5B%7B%22fallback%22%3A%22a7%22%2C%22pretext%22%3A%22a9%22%2C%22author_name%22%3A%22a6%22%2C%22author_link%22%3A%22a5%22%2C%22author_icon%22%3A%22a4%22%2C%22title%22%3A%22a2%22%2C%22title_link%22%3A%22a10%22%2C%22text%22%3A%22a1%22%2C%22color%22%3A%22a3%22%2C%22fields%22%3A%5B%7B%22title%22%3A%22f1%22%2C%22value%22%3A%22f2%22%2C%22short%22%3Atrue%7D%5D%2C%22mrkdwn_in%22%3A%5B%22a8%22%5D%7D%5D%7D');
    }
}
