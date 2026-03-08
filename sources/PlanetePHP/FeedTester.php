<?php

declare(strict_types=1);

namespace PlanetePHP;

use Laminas\Feed\Reader\Http\ClientInterface;
use SimpleXMLElement;

final readonly class FeedTester
{
    public function __construct(
        private ClientInterface $feedClient,
    ) {}

    public function test(Feed $feed): bool
    {
        try {
            $xml = $this->feedClient->get($feed->feed);

            new SimpleXmlElement($xml->getBody());

            return true;
        } catch (\Exception) {
            return false;
        }
    }
}
