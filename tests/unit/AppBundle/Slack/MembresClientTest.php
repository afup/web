<?php

declare(strict_types=1);

namespace AppBundle\Tests\Slack;

use AppBundle\Slack\MembresClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;

final class MembresClientTest extends TestCase
{
    public function testPostMessage(): void
    {
        $response = new JsonMockResponse(['ok' => true]);
        $client = new MembresClient('xoxb-token', 'https://slack.test/api', new MockHttpClient([$response]));

        $client->postMessage('C0123ABCDEF', 'Fallback', [['type' => 'divider']]);

        self::assertSame('POST', $response->getRequestMethod());
        self::assertSame('https://slack.test/api/chat.postMessage', $response->getRequestUrl());
        self::assertContains('Authorization: Bearer xoxb-token', $response->getRequestOptions()['headers']);
        self::assertSame(
            [
                'channel' => 'C0123ABCDEF',
                'text' => 'Fallback',
                'unfurl_links' => false,
                'unfurl_media' => false,
                'blocks' => [['type' => 'divider']],
            ],
            json_decode($response->getRequestOptions()['body'], true),
        );
    }

    public function testPostMessageWithoutBlocks(): void
    {
        $response = new JsonMockResponse(['ok' => true]);
        $client = new MembresClient('xoxb-token', 'https://slack.test/api', new MockHttpClient([$response]));

        $client->postMessage('C0123ABCDEF', 'Fallback');

        self::assertArrayNotHasKey('blocks', json_decode($response->getRequestOptions()['body'], true));
    }

    public function testPostMessageThrowsOnSlackError(): void
    {
        $client = new MembresClient('xoxb-token', 'https://slack.test/api', new MockHttpClient([
            new JsonMockResponse(['ok' => false, 'error' => 'not_in_channel']),
        ]));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('not_in_channel');

        $client->postMessage('C0123ABCDEF', 'Fallback');
    }
}
