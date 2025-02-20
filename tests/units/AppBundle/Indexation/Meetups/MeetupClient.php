<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups\tests\units;

use AppBundle\Antennes\AntennesCollection;
use AppBundle\Indexation\Meetups\MeetupClient as TestedClass;
use atoum;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class MeetupClient extends atoum
{
    public function testFailedHttpCall(): void
    {
        $httpClient = $this->makeGuzzleMockClient(
            new Response(500)
        );

        $meetupClient = new TestedClass($httpClient, new AntennesCollection());

        $this
            ->exception(fn () => $meetupClient->getEvents())
            ->isInstanceOf(Exception::class)
            ->hasMessage("Server error: `POST https://api.meetup.com/gql` resulted in a `500 Internal Server Error` response");
    }

    public function testInvalidJsonInResponse(): void
    {
        $httpClient = $this->makeGuzzleMockClient(
            new Response(200, [], 'invalid json')
        );

        $meetupClient = new TestedClass($httpClient, new AntennesCollection());

        $this
            ->exception(fn () => $meetupClient->getEvents())
            ->isInstanceOf(Exception::class)
            ->hasMessage('The given value is not a valid JSON entry.');
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

        $meetupClient = new TestedClass($httpClient, new AntennesCollection());

        $antennes = $meetupClient->getEvents();

        $this->integer(count($antennes))->isEqualTo(4);

        $this->integer($antennes[0]->getId())->isEqualTo(12);
        $this->string($antennes[0]->getTitle())->isEqualTo('Upcoming 1');
        $this->string($antennes[0]->getDescription())->isEqualTo('Desc 1');
        $this->dateTime($antennes[0]->getDate())->isEqualTo(new \DateTime('2025-02-11T18:30+01:00'));
        $this->string($antennes[0]->getAntenneName())->isEqualTo('lyon');
        $this->string($antennes[0]->getLocation())->isEqualTo('Lieu 1');

        $this->integer($antennes[1]->getId())->isEqualTo(34);
        $this->string($antennes[1]->getTitle())->isEqualTo('Upcoming 2');
        $this->string($antennes[1]->getDescription())->isEqualTo('Desc 2');
        $this->dateTime($antennes[1]->getDate())->isEqualTo(new \DateTime('2025-03-20T18:30+01:00'));
        $this->string($antennes[1]->getAntenneName())->isEqualTo('lyon');
        $this->variable($antennes[1]->getLocation())->isNull();

        $this->integer($antennes[2]->getId())->isEqualTo(56);
        $this->string($antennes[2]->getTitle())->isEqualTo('Past 1');
        $this->string($antennes[2]->getDescription())->isEqualTo('Desc 3');
        $this->dateTime($antennes[2]->getDate())->isEqualTo(new \DateTime('2019-04-08T18:30+01:00'));
        $this->string($antennes[2]->getAntenneName())->isEqualTo('lyon');
        $this->variable($antennes[2]->getLocation())->isNull();

        $this->integer($antennes[3]->getId())->isEqualTo(78);
        $this->string($antennes[3]->getTitle())->isEqualTo('Past 2');
        $this->string($antennes[3]->getDescription())->isEqualTo('Desc 4');
        $this->dateTime($antennes[3]->getDate())->isEqualTo(new \DateTime('2020-10-17T18:30+01:00'));
        $this->string($antennes[3]->getAntenneName())->isEqualTo('lyon');
        $this->string($antennes[3]->getLocation())->isEqualTo('Lieu 2');
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
