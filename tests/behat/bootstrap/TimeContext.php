<?php

declare(strict_types=1);

namespace Afup\Tests\Behat\Bootstrap;

use Afup\Tests\Support\TimeMocker;
use Behat\Hook\BeforeScenario;
use Behat\Step\Given;
use Behat\Step\Then;
use DateTimeImmutable;
use DateTimeInterface;

trait TimeContext
{
    private TimeMocker $timeMocker;

    private function initTimeContext(): void
    {
        $this->timeMocker = new TimeMocker();
    }

    #[BeforeScenario]
    public function clearTestClock(): void
    {
        $this->timeMocker->clearCurrentDateMock();
    }

    #[Given('/^the current date is "(?P<date>[^"]*)"$/')]
    public function theCurrentDateIs(string $date): void
    {
        $this->timeMocker->setCurrentDateMock($date);
    }

    #[Then('the response should contain date :arg1')]
    #[Then('the response should contain date :arg1 with format :arg2')]
    public function responseShouldContainsDate(string $datetimeFormat, string $withFormat = DateTimeInterface::ATOM): void
    {
        $this->minkContext->assertResponseContains((new DateTimeImmutable($datetimeFormat))->format($withFormat));
    }
}
