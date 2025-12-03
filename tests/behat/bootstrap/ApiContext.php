<?php

declare(strict_types=1);

namespace Afup\Tests\Behat\Bootstrap;

use Behat\Step\Then;
use PHPUnit\Framework\Assert;
use Symfony\Component\JsonPath\JsonCrawler;

trait ApiContext
{
    #[Then('/^the response should be in json$/')]
    public function assertResponseShouldBeInJson(): void
    {
        $this->assertResponseHeaderEquals('Content-Type', 'application/json');
        Assert::assertJson($this->minkContext->getSession()->getPage()->getContent());
    }

    #[Then('/^the json response has the key "(?P<key>[^"]*)" with value "(?P<value>(?:[^"]|\\")*)"$/')]
    public function assertResponseHasJsonKeyAndValue(string $key, string $value): void
    {
        $crawler = new JsonCrawler($this->minkContext->getSession()->getPage()->getContent());

        $foundValue = $crawler->find($key);

        Assert::assertCount(1, $foundValue);
        Assert::assertSame($value, $foundValue[0]);
    }

    #[Then('/^the json response has no key "(?P<key>[^"]*)"$/')]
    public function assertResponseHasNoJsonKey(string $key): void
    {
        $crawler = new JsonCrawler($this->minkContext->getSession()->getPage()->getContent());

        $foundValue = $crawler->find($key);

        Assert::assertEmpty($foundValue);
    }
}
