<?php

declare(strict_types=1);

namespace Afup\Tests\Behat\Bootstrap;

use Behat\Step\Then;
use Symfony\Component\JsonPath\JsonCrawler;
use Webmozart\Assert\Assert;

trait ApiContext
{
    #[Then('/^the response should be in json$/')]
    public function assertResponseShouldBeInJson(): void
    {
        $this->assertResponseHeaderEquals('Content-Type', 'application/json');

        $decoded = json_decode($this->minkContext->getSession()->getPage()->getContent(), true);

        Assert::isArray($decoded);
    }

    #[Then('/^the json response has the key "(?P<key>[^"]*)" with value "(?P<value>(?:[^"]|\\")*)"$/')]
    public function assertResponseHasJsonKeyAndValue(string $key, string $value): void
    {
        $crawler = new JsonCrawler($this->minkContext->getSession()->getPage()->getContent());

        $foundValue = $crawler->find($key);

        Assert::count($foundValue, 1);
        Assert::same($foundValue[0], $value);
    }

    #[Then('/^the json response has no key "(?P<key>[^"]*)"$/')]
    public function assertResponseHasNoJsonKey(string $key): void
    {
        $crawler = new JsonCrawler($this->minkContext->getSession()->getPage()->getContent());

        $foundValue = $crawler->find($key);

        Assert::isEmpty($foundValue);
    }
}
