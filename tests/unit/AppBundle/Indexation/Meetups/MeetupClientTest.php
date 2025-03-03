<?php

declare(strict_types=1);

namespace AppBundle\Tests\Indexation\Meetups;

use AppBundle\Antennes\AntennesCollection;
use AppBundle\Indexation\Meetups\MeetupClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class MeetupClientTest extends TestCase
{
    /**
     * @dataProvider failureDataProvider
     */
    public function testFailure(Response $response, string $expectedExceptionMessage): void
    {
        $httpClient = $this->makeGuzzleMockClient($response);

        $meetupClient = new MeetupClient($httpClient, new AntennesCollection());

        self::expectException(\Exception::class);
        self::expectExceptionMessage($expectedExceptionMessage);

        $meetupClient->getEvents();
    }

    public function failureDataProvider(): \Generator
    {
        yield [
            'response' => new Response(500),
            'exception' => "Server error: `POST https://api.meetup.com/gql` resulted in a `500 Internal Server Error` response",
        ];

        yield [
            'response' => new Response(200, [], 'invalid json'),
            'exception' => 'The given value is not a valid JSON entry.',
        ];
    }

    public function testReturnsValidResponse(): void
    {
        $httpClient = $this->makeGuzzleMockClient(
            new Response(
                200,
                [],
                json_encode([
                    'data' => [
                        'lyon' => [
                            'upcomingEvents' => [
                                'edges' => [
                                    [
                                        'node' => [
                                            'id' => "12",
                                            'title' => 'Upcoming 1',
                                            'description' => 'Desc 1',
                                            'dateTime' => '2025-02-11T18:30+01:00',
                                            'venue' => [
                                                'name' => 'Lieu 1',
                                            ],
                                        ],
                                    ],
                                    [
                                        'node' => [
                                            'id' => "34",
                                            'title' => 'Upcoming 2',
                                            'description' => 'Desc 2',
                                            'dateTime' => '2025-03-20T18:30+01:00',
                                            'venue' => null,
                                        ],
                                    ],
                                ],
                            ],
                            'pastEvents' => [
                                'edges' => [
                                    [
                                        'node' => [
                                            'id' => "56",
                                            'title' => 'Past 1',
                                            'description' => 'Desc 3',
                                            'dateTime' => '2019-04-08T18:30+01:00',
                                            'venue' => null,
                                        ],
                                    ],
                                    [
                                        'node' => [
                                            'id' => "78",
                                            'title' => 'Past 2',
                                            'description' => 'Desc 4',
                                            'dateTime' => '2020-10-17T18:30+01:00',
                                            'venue' => [
                                                'name' => 'Lieu 2',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]),
            ),
        );

        $meetupClient = new MeetupClient($httpClient, new AntennesCollection());

        $antennes = $meetupClient->getEvents();

        self::assertCount(4, $antennes);

        self::assertEquals(12, $antennes[0]->getId());
        self::assertEquals('Upcoming 1', $antennes[0]->getTitle());
        self::assertEquals('Desc 1', $antennes[0]->getDescription());
        self::assertEquals(new \DateTime('2025-02-11T18:30+01:00'), $antennes[0]->getDate());
        self::assertEquals('lyon', $antennes[0]->getAntenneName());
        self::assertEquals('Lieu 1', $antennes[0]->getLocation());

        self::assertEquals(34, $antennes[1]->getId());
        self::assertEquals('Upcoming 2', $antennes[1]->getTitle());
        self::assertEquals('Desc 2', $antennes[1]->getDescription());
        self::assertEquals(new \DateTime('2025-03-20T18:30+01:00'), $antennes[1]->getDate());
        self::assertEquals('lyon', $antennes[1]->getAntenneName());
        self::assertNull($antennes[1]->getLocation());

        self::assertEquals(56, $antennes[2]->getId());
        self::assertEquals('Past 1', $antennes[2]->getTitle());
        self::assertEquals('Desc 3', $antennes[2]->getDescription());
        self::assertEquals(new \DateTime('2019-04-08T18:30+01:00'), $antennes[2]->getDate());
        self::assertEquals('lyon', $antennes[2]->getAntenneName());
        self::assertNull($antennes[2]->getLocation());

        self::assertEquals(78, $antennes[3]->getId());
        self::assertEquals('Past 2', $antennes[3]->getTitle());
        self::assertEquals('Desc 4', $antennes[3]->getDescription());
        self::assertEquals(new \DateTime('2020-10-17T18:30+01:00'), $antennes[3]->getDate());
        self::assertEquals('lyon', $antennes[3]->getAntenneName());
        self::assertEquals('Lieu 2', $antennes[3]->getLocation());
    }

    private function makeGuzzleMockClient(Response $response): Client
    {
        return new Client([
            'handler' => HandlerStack::create(
                new MockHandler([$response])
            )
        ]);
    }
}
