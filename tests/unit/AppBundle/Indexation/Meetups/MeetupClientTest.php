<?php

declare(strict_types=1);

namespace AppBundle\Tests\Indexation\Meetups;

use AppBundle\Antennes\AntenneRepository;
use AppBundle\Indexation\Meetups\MeetupClient;
use CuyZ\Valinor\MapperBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class MeetupClientTest extends TestCase
{
    #[DataProvider('failureDataProvider')]
    public function testFailure(MockResponse $response, string $expectedExceptionMessage): void
    {
        $httpClient = $this->makeGuzzleMockClient($response);

        $meetupClient = new MeetupClient($httpClient, new AntenneRepository(), new MapperBuilder());

        self::expectException(\Exception::class);
        self::expectExceptionMessage($expectedExceptionMessage);

        $meetupClient->getEvents();
    }

    public static function failureDataProvider(): \Generator
    {
        yield [
            'response' => new MockResponse('', ['http_code' => 500]),
            'expectedExceptionMessage' => 'HTTP 500 returned for "http://fakemeetup/gql-ext".',
        ];

        yield [
            'response' => new MockResponse('invalid json'),
            'expectedExceptionMessage' => 'Syntax error for "http://fakemeetup/gql-ext".',
        ];
    }

    public function testReturnsValidResponse(): void
    {
        $httpClient = $this->makeGuzzleMockClient(
            new MockResponse(
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
                                            'dateTime' => '2025-02-11T18:30:00+01:00',
                                            'venue' => [
                                                'name' => 'Lieu 1',
                                            ],
                                            'displayPhoto' => [
                                                'standardUrl' => 'https://example.com/1',
                                            ],
                                        ],
                                    ],
                                    [
                                        'node' => [
                                            'id' => "34",
                                            'title' => 'Upcoming 2',
                                            'description' => 'Desc 2',
                                            'dateTime' => '2025-03-20T18:30:00+01:00',
                                            'venue' => [
                                                'name' => null,
                                            ],
                                            'displayPhoto' => [
                                                'standardUrl' => 'https://example.com/2',
                                            ],
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
                                            'dateTime' => '2019-04-08T18:30:00+01:00',
                                            'venue' => [
                                                'name' => null,
                                            ],
                                            'displayPhoto' => [
                                                'standardUrl' => 'https://example.com/3',
                                            ],
                                        ],
                                    ],
                                    [
                                        'node' => [
                                            'id' => "78",
                                            'title' => 'Past 2',
                                            'description' => 'Desc 4',
                                            'dateTime' => '2020-10-17T18:30:00+01:00',
                                            'venue' => [
                                                'name' => 'Lieu 2',
                                            ],
                                            'displayPhoto' => [
                                                'standardUrl' => 'https://example.com/4',
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

        $meetupClient = new MeetupClient($httpClient, new AntenneRepository(), new MapperBuilder());

        $antennes = $meetupClient->getEvents();

        self::assertCount(4, $antennes);

        self::assertEquals(12, $antennes[0]->id);
        self::assertEquals('Upcoming 1', $antennes[0]->titre);
        self::assertEquals('Desc 1', $antennes[0]->description);
        self::assertEquals(new \DateTime('2025-02-11T18:30:00+01:00'), $antennes[0]->date);
        self::assertEquals('lyon', $antennes[0]->codeAntenne);
        self::assertEquals('Lieu 1', $antennes[0]->lieu);
        self::assertEquals('https://example.com/1', $antennes[0]->photoUrl);

        self::assertEquals(34, $antennes[1]->id);
        self::assertEquals('Upcoming 2', $antennes[1]->titre);
        self::assertEquals('Desc 2', $antennes[1]->description);
        self::assertEquals(new \DateTime('2025-03-20T18:30:00+01:00'), $antennes[1]->date);
        self::assertEquals('lyon', $antennes[1]->codeAntenne);
        self::assertNull($antennes[1]->lieu);
        self::assertEquals('https://example.com/2', $antennes[1]->photoUrl);

        self::assertEquals(56, $antennes[2]->id);
        self::assertEquals('Past 1', $antennes[2]->titre);
        self::assertEquals('Desc 3', $antennes[2]->description);
        self::assertEquals(new \DateTime('2019-04-08T18:30:00+01:00'), $antennes[2]->date);
        self::assertEquals('lyon', $antennes[2]->codeAntenne);
        self::assertNull($antennes[2]->lieu);
        self::assertEquals('https://example.com/3', $antennes[2]->photoUrl);

        self::assertEquals(78, $antennes[3]->id);
        self::assertEquals('Past 2', $antennes[3]->titre);
        self::assertEquals('Desc 4', $antennes[3]->description);
        self::assertEquals(new \DateTime('2020-10-17T18:30:00+01:00'), $antennes[3]->date);
        self::assertEquals('lyon', $antennes[3]->codeAntenne);
        self::assertEquals('Lieu 2', $antennes[3]->lieu);
        self::assertEquals('https://example.com/4', $antennes[3]->photoUrl);
    }

    private function makeGuzzleMockClient(ResponseInterface $response): HttpClientInterface
    {
        return new MockHttpClient([$response], 'http://fakemeetup');
    }
}
