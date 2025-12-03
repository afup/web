<?php

declare(strict_types=1);

namespace Afup\Tests\Behat\Bootstrap;

use AppBundle\Listener\DetectClockMockingListener;
use Behat\Hook\BeforeScenario;
use Behat\Step\Given;
use Behat\Step\Then;
use DateTimeImmutable;
use DateTimeInterface;

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

    #[Then('the response should contain date :arg1')]
    #[Then('the response should contain date :arg1 with format :arg2')]
    public function responseShouldContainsDate(string $datetimeFormat, string $withFormat = DateTimeInterface::ATOM): void
    {
        $this->minkContext->assertResponseContains((new DateTimeImmutable($datetimeFormat))->format($withFormat));
    }
}
