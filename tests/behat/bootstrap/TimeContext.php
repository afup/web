<?php

declare(strict_types=1);

namespace Afup\Tests\Behat\Bootstrap;

use AppBundle\Listener\DetectClockMockingListener;
use Behat\Hook\BeforeScenario;
use Behat\Step\Given;

trait TimeContext
{
    #[BeforeScenario]
    public function clearTestClock(): void
    {
        $this->minkContext->getSession()->getDriver()->setRequestHeader(DetectClockMockingListener::HEADER, '');
    }

    #[Given('/^the current date is "(?P<date>[^"]*)"$/')]
    public function theCurrentDateIs(string $date): void
    {
        $this->minkContext->getSession()->getDriver()->setRequestHeader(DetectClockMockingListener::HEADER, $date);
    }
}
