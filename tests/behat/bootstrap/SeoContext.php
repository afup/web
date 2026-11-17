<?php

declare(strict_types=1);

namespace Afup\Tests\Behat\Bootstrap;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use Behat\Step\Then;

trait SeoContext
{
    #[Then('the page should contain at least :count links to other talks')]
    public function assertPageContainsLinksToOtherTalks(int $count): void
    {
        $session = $this->minkContext->getSession();

        preg_match('#^/talks/(\d+)-#', (string) parse_url($session->getCurrentUrl(), PHP_URL_PATH), $matches);
        $currentTalkId = $matches[1] ?? null;

        $found = 0;
        foreach ($session->getPage()->findAll('css', 'a[href]') as $link) {
            $path = (string) parse_url((string) $link->getAttribute('href'), PHP_URL_PATH);
            if (preg_match('#^/talks/(\d+)-[^/]+$#', $path, $matches) && $matches[1] !== $currentTalkId) {
                ++$found;
            }
        }

        if ($found < $count) {
            throw new ExpectationException(
                sprintf('Expected at least %d links to other talks, found %d', $count, $found),
                $session->getDriver(),
            );
        }
    }

    #[Then('the JSON-LD :type should have the following items:')]
    public function assertJsonLdHasItems(string $type, TableNode $expectedItems): void
    {
        $session = $this->minkContext->getSession();

        $items = null;
        foreach ($session->getPage()->findAll('css', 'script[type="application/ld+json"]') as $script) {
            $data = json_decode($script->getHtml(), true, flags: JSON_THROW_ON_ERROR);
            if (($data['@type'] ?? null) === $type) {
                $items = $data['itemListElement'] ?? [];
                break;
            }
        }

        if ($items === null) {
            throw new ExpectationException(sprintf('No JSON-LD of type "%s" found', $type), $session->getDriver());
        }

        $actual = array_map(
            fn(array $item): array => [
                'position' => (string) $item['position'],
                'name' => $item['name'],
                'item' => $item['item'],
            ],
            $items,
        );

        if ($actual !== $expectedItems->getHash()) {
            throw new ExpectationException(
                sprintf('JSON-LD "%s" items do not match: %s', $type, json_encode($actual, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)),
                $session->getDriver(),
            );
        }
    }
}
