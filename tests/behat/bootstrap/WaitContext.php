<?php

declare(strict_types=1);

namespace Afup\Tests\Behat\Bootstrap;

use Behat\Mink\Exception\ExpectationException;
use Behat\Step\Then;
use Behat\Step\When;

/**
 * Provides robust wait mechanisms for browser-based tests.
 *
 * Instead of using static sleep() calls which are flaky, these methods
 * poll for conditions with a timeout, making tests more reliable.
 */
trait WaitContext
{
    private const DEFAULT_TIMEOUT_MS = 10000;
    private const DEFAULT_POLL_INTERVAL_MS = 100;

    /**
     * Wait until text appears on the page.
     *
     * @example Then I wait until I see "L'écriture a été ajoutée"
     * @example And I wait until I see "Success message" within 5000ms
     */
    // #[Then('/^I wait until I see "(?P<text>[^"]*)"(?:| within (?P<timeout>\d+)ms)$/')]
    #[When('/^I wait until I see "(?P<text>[^"]*)"(?:| within (?P<timeout>\d+)ms)$/')]
    public function waitToSeeText(string $text, ?string $timeout = null): void
    {
        $timeoutMs = $timeout !== null ? (int) $timeout : self::DEFAULT_TIMEOUT_MS;

        $this->waitForCondition(
            fn() => str_contains($this->minkContext->getSession()->getPage()->getText(), $text),
            sprintf('Text "%s" did not appear within %dms', $text, $timeoutMs),
            $timeoutMs,
        );
    }

    /**
     * Wait until text disappears from the page.
     *
     * @example Then I wait until I do not see "Loading..."
     */
    // #[Then('/^I wait until I do not see "(?P<text>[^"]*)"(?:| within (?P<timeout>\d+)ms)$/')]
    #[When('/^I wait until I do not see "(?P<text>[^"]*)"(?:| within (?P<timeout>\d+)ms)$/')]
    public function waitToNotSeeText(string $text, ?string $timeout = null): void
    {
        $timeoutMs = $timeout !== null ? (int) $timeout : self::DEFAULT_TIMEOUT_MS;

        $this->waitForCondition(
            fn() => !str_contains($this->minkContext->getSession()->getPage()->getText(), $text),
            sprintf('Text "%s" did not disappear within %dms', $text, $timeoutMs),
            $timeoutMs,
        );
    }

    /**
     * Press a button and wait for a specific text to appear.
     * This is a convenience method combining button press with waiting.
     *
     * @example When I press "Submit" and wait until I see "Success"
     */
    #[When('/^I press "(?P<button>[^"]*)" and wait until I see "(?P<text>[^"]*)"(?:| within (?P<timeout>\d+)ms)$/')]
    public function pressButtonAndWaitToSee(string $button, string $text, ?string $timeout = null): void
    {
        $this->minkContext->pressButton($button);
        $this->waitToSeeText($text, $timeout);
    }

    /**
     * Generic wait-for-condition method that polls until timeout.
     *
     * @param callable $condition Function that returns true when condition is met
     * @param string $errorMessage Error message if timeout is reached
     * @param int $timeoutMs Maximum time to wait in milliseconds
     * @throws ExpectationException If condition is not met within timeout
     */
    private function waitForCondition(callable $condition, string $errorMessage, int $timeoutMs = self::DEFAULT_TIMEOUT_MS): void
    {
        $start = microtime(true) * 1000;
        $end = $start + $timeoutMs;

        while (microtime(true) * 1000 < $end) {
            try {
                if ($condition()) {
                    return;
                }
            } catch (\Exception $e) {
                // Ignore exceptions during polling (element might not exist yet)
            }

            usleep(self::DEFAULT_POLL_INTERVAL_MS * 1000);
        }

        throw new ExpectationException($errorMessage, $this->minkContext->getSession()->getDriver());
    }
}
