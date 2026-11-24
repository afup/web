<?php

declare(strict_types=1);

namespace AppBundle\Tests\Joindin;

use AppBundle\Event\Model\Talk;
use AppBundle\Joindin\JoindinTalk;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class JoindinTalkTest extends TestCase
{
    public function testReturnsStubFromValidPayload(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url): MockResponse {
            self::assertSame('https://api.joind.in/v2.1/talks/24041', $url);

            return new MockResponse('{"talks": [{"stub": "abc12"}]}');
        }, 'https://api.joind.in');

        self::assertSame('abc12', new JoindinTalk($httpClient, new ArrayAdapter())->getStubFromTalk($this->talk()));
    }

    public function testReturnsNullOnTransportError(): void
    {
        $httpClient = new MockHttpClient(new MockResponse(info: ['error' => 'DNS failure']), 'https://api.joind.in');

        self::assertNull(new JoindinTalk($httpClient, new ArrayAdapter())->getStubFromTalk($this->talk()));
    }

    public function testReturnsNullOnServerErrorAndDoesNotCacheIt(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('Oops', ['http_code' => 500]),
            new MockResponse('{"talks": [{"stub": "abc12"}]}'),
        ], 'https://api.joind.in');
        $joindinTalk = new JoindinTalk($httpClient, new ArrayAdapter());

        self::assertNull($joindinTalk->getStubFromTalk($this->talk()));
        self::assertSame('abc12', $joindinTalk->getStubFromTalk($this->talk()));
    }

    public function testReturnsNullOnInvalidJson(): void
    {
        $httpClient = new MockHttpClient(new MockResponse('not json'), 'https://api.joind.in');

        self::assertNull(new JoindinTalk($httpClient, new ArrayAdapter())->getStubFromTalk($this->talk()));
    }

    public function testDoesNotCallApiWithoutJoindinId(): void
    {
        $httpClient = new MockHttpClient();

        self::assertNull(new JoindinTalk($httpClient, new ArrayAdapter())->getStubFromTalk(new Talk()));
        self::assertSame(0, $httpClient->getRequestsCount());
    }

    private function talk(): Talk
    {
        return new Talk()->setJoindinId(24041);
    }
}
