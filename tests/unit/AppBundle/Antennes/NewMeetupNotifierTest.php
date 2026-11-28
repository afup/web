<?php

declare(strict_types=1);

namespace AppBundle\Tests\Antennes;

use AppBundle\Antennes\Entity\Antenne;
use AppBundle\Antennes\Meetup as AntenneMeetup;
use AppBundle\Antennes\NewMeetupNotifier;
use AppBundle\Antennes\ReseauxSociaux;
use AppBundle\Event\Entity\Meetup;
use AppBundle\Slack\MembresClient;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;

final class NewMeetupNotifierTest extends TestCase
{
    private const string NOW = '2050-01-01 12:00:00';

    public function testPostsMessageOnAntenneChannel(): void
    {
        $response = new JsonMockResponse(['ok' => true]);
        $client = new MockHttpClient([$response]);
        $notifier = $this->createNotifier($client);

        $meetup = $this->createMeetup('2050-02-14 19:00:00');
        $meetup->lieu = 'Le Grand Comptoir';
        $meetup->photoUrl = 'https://secure.meetupstatic.com/photo.jpg';

        $notifier->notify($meetup, $this->createAntenne('C0123ABCDEF'));

        self::assertSame(1, $client->getRequestsCount());
        $body = json_decode($response->getRequestOptions()['body'], true);

        self::assertSame('C0123ABCDEF', $body['channel']);
        self::assertSame(
            'Nouveau meetup AFUP Reims : Apéro <PHP> & co, le 14/02/2050 à 19h00 – https://www.meetup.com/fr-FR/afup-reims-php/events/244992881',
            $body['text'],
        );
        self::assertSame(
            ":tada: Nouveau meetup AFUP Reims\n"
            . "*<https://www.meetup.com/fr-FR/afup-reims-php/events/244992881|Apéro &lt;PHP&gt; &amp; co>*\n"
            . ":date: 14/02/2050 à 19h00\n"
            . ':round_pushpin: Le Grand Comptoir',
            $body['blocks'][0]['text']['text'],
        );
        self::assertSame('https://secure.meetupstatic.com/photo.jpg', $body['blocks'][0]['accessory']['image_url']);
        self::assertSame('https://www.meetup.com/fr-FR/afup-reims-php/events/244992881', $body['blocks'][1]['elements'][0]['url']);
    }

    public function testDoesNothingWithoutChannel(): void
    {
        $client = new MockHttpClient([]);

        $this->createNotifier($client)->notify($this->createMeetup('2050-02-14 19:00:00'), $this->createAntenne(null));

        self::assertSame(0, $client->getRequestsCount());
    }

    public function testDoesNothingForPastMeetup(): void
    {
        $client = new MockHttpClient([]);

        $this->createNotifier($client)->notify($this->createMeetup('2049-12-31 19:00:00'), $this->createAntenne('C0123ABCDEF'));

        self::assertSame(0, $client->getRequestsCount());
    }

    public function testSlackErrorIsLoggedAndSwallowed(): void
    {
        $client = new MockHttpClient([new JsonMockResponse(['ok' => false, 'error' => 'not_in_channel'])]);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('error')
            ->with(self::anything(), self::callback(
                static fn(array $context): bool => $context['meetup'] === 244992881
                    && str_contains($context['message'], 'not_in_channel'),
            ));

        $this->createNotifier($client, $logger)->notify($this->createMeetup('2050-02-14 19:00:00'), $this->createAntenne('C0123ABCDEF'));
    }

    private function createNotifier(MockHttpClient $httpClient, ?LoggerInterface $logger = null): NewMeetupNotifier
    {
        $notifier = new NewMeetupNotifier(
            new MembresClient('xoxb-token', 'https://slack.test/api', $httpClient),
            $logger ?? new NullLogger(),
        );
        $notifier->setClock(new MockClock(self::NOW));

        return $notifier;
    }

    private function createMeetup(string $date): Meetup
    {
        $meetup = new Meetup();
        $meetup->id = 244992881;
        $meetup->date = new \DateTimeImmutable($date);
        $meetup->titre = 'Apéro <PHP> & co';
        $meetup->codeAntenne = 'reims';

        return $meetup;
    }

    private function createAntenne(?string $slackChannel): Antenne
    {
        return new Antenne(
            'reims',
            'Reims',
            new AntenneMeetup('afup-reims-php', '18197674'),
            '/images/offices/reims.svg',
            new ReseauxSociaux(),
            null,
            slackMembresIdCanal: $slackChannel,
        );
    }
}
