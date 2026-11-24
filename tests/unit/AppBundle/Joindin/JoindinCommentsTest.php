<?php

declare(strict_types=1);

namespace AppBundle\Tests\Joindin;

use AppBundle\Event\Model\Talk;
use AppBundle\Joindin\JoindinComments;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class JoindinCommentsTest extends TestCase
{
    public function testReturnsCommentsFromValidPayload(): void
    {
        $httpClient = new MockHttpClient(new MockResponse(json_encode([
            'comments' => [
                ['comment' => 'Super talk', 'user_display_name' => 'Alice', 'created_date' => '2024-01-01T10:00:00+01:00', 'rating' => 5],
                ['comment' => 'Bof', 'user_display_name' => '', 'created_date' => '2024-01-02T10:00:00+01:00', 'rating' => 2],
            ],
        ], JSON_THROW_ON_ERROR)), 'https://api.joind.in');

        $comments = new JoindinComments($httpClient, new ArrayAdapter())->getCommentsFromTalk($this->talk(24041));

        self::assertSame([
            ['comment' => 'Super talk', 'user_display_name' => 'Alice', 'created_date' => '2024-01-01T10:00:00+01:00', 'rating' => 5],
            ['comment' => 'Bof', 'user_display_name' => 'Anonyme', 'created_date' => '2024-01-02T10:00:00+01:00', 'rating' => 2],
        ], $comments);
        self::assertSame(1, $httpClient->getRequestsCount());
    }

    public function testReturnsEmptyArrayOnTransportError(): void
    {
        $httpClient = new MockHttpClient(new MockResponse(info: ['error' => 'DNS failure']), 'https://api.joind.in');

        self::assertSame([], new JoindinComments($httpClient, new ArrayAdapter())->getCommentsFromTalk($this->talk(24041)));
    }

    public function testReturnsEmptyArrayOnServerErrorAndDoesNotCacheIt(): void
    {
        $cache = new ArrayAdapter();
        $httpClient = new MockHttpClient([
            new MockResponse('Oops', ['http_code' => 500]),
            new MockResponse('{"comments": []}'),
        ], 'https://api.joind.in');
        $joindinComments = new JoindinComments($httpClient, $cache);

        self::assertSame([], $joindinComments->getCommentsFromTalk($this->talk(24041)));
        self::assertSame([], $joindinComments->getCommentsFromTalk($this->talk(24041)));
        self::assertSame(2, $httpClient->getRequestsCount());
    }

    public function testReturnsEmptyArrayOnInvalidJson(): void
    {
        $httpClient = new MockHttpClient(new MockResponse('not json'), 'https://api.joind.in');

        self::assertSame([], new JoindinComments($httpClient, new ArrayAdapter())->getCommentsFromTalk($this->talk(24041)));
    }

    public function testDoesNotCallApiWithoutJoindinId(): void
    {
        $httpClient = new MockHttpClient();

        self::assertSame([], new JoindinComments($httpClient, new ArrayAdapter())->getCommentsFromTalk(new Talk()));
        self::assertSame(0, $httpClient->getRequestsCount());
    }

    public function testCallsExpectedUrl(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url): MockResponse {
            self::assertSame('GET', $method);
            self::assertSame('https://api.joind.in/v2.1/talks/24041/comments?resultsperpage=0', $url);

            return new MockResponse('{"comments": []}');
        }, 'https://api.joind.in');

        new JoindinComments($httpClient, new ArrayAdapter())->getCommentsFromTalk($this->talk(24041));

        self::assertSame(1, $httpClient->getRequestsCount());
    }

    private function talk(int $joindinId): Talk
    {
        return new Talk()->setJoindinId($joindinId);
    }
}
