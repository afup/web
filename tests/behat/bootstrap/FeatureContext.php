<?php

declare(strict_types=1);

namespace Afup\Tests\Behat\Bootstrap;

use Afup\Tests\Support\DatabaseManager;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Hook\BeforeScenario;
use Behat\Mink\Exception\ExpectationException;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Step\Then;
use Behat\Step\When;

class FeatureContext implements Context
{
    use ApiContext;
    use AuthContext;
    use EmailContext;
    use FormContext;
    use PdfContext;
    use TimeContext;
    use WaitContext;

    private MinkContext $minkContext;

    private readonly DatabaseManager $databaseManager;

    public function __construct()
    {
        $this->databaseManager = new DatabaseManager(true);
    }

    #[BeforeScenario]
    public function fetchMinkContext(BeforeScenarioScope $scope): void
    {
        $this->minkContext = $scope->getEnvironment()->getContext(MinkContext::class);
    }

    #[BeforeScenario('@reloadDbWithTestData')]
    public function beforeScenarioReloadDatabase(): void
    {
        $this->databaseManager->reloadDatabase();
    }

    #[Then('simulate the Paybox callback')]
    public function simulateThePayboxCallback(): void
    {
        $url = $this->minkContext->getSession()->getCurrentUrl();
        $url = str_replace('paybox-redirect', 'paybox-callback', $url);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_exec($curl);
    }

    #[Then('the response header :arg1 should equal :arg2')]
    public function assertResponseHeaderEquals(string $headerName, string $expectedValue): void
    {
        $this->minkContext->assertSession()->responseHeaderEquals($headerName, $expectedValue);
    }

    #[Then('the response header :arg1 should match :arg2')]
    public function assertResponseHeaderMatch(string $headerName, string $regExpExpectedValue): void
    {
        $this->minkContext->assertSession()->responseHeaderMatches($headerName, $regExpExpectedValue);
    }

    #[Then('/^the response should contain the html "(?P<text>(?:[^"]|\\")*)"$/')]
    #[Then('/^the response should contain the html$/')]
    public function assertResponseHasHtml(PyStringNode|string $html): void
    {
        if ($html instanceof PyStringNode) {
            $html = $html->getRaw();
        }

        $this->minkContext->assertResponseContains($html);
    }

    #[Then('the current URL should match :arg1')]
    public function assertCurrentUrlContains(string $regex): void
    {
        $currentUrl = $this->minkContext->getSession()->getCurrentUrl();

        if (!preg_match($regex, $currentUrl)) {
            throw new ExpectationException(
                sprintf('The current URL "%s" does not matches "%s"', $currentUrl, $regex),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[When('I follow the button of tooltip :arg1')]
    public function clickLinkOfTooltip(string $tooltip): void
    {
        $link = $this->minkContext->getSession()->getPage()->find('css', sprintf('a[data-tooltip="%s"]', $tooltip));

        if (null === $link) {
            throw new ExpectationException(
                sprintf('Link of tooltip "%s" not found', $tooltip),
                $this->minkContext->getSession()->getDriver(),
            );
        }

        $link->click();
    }

    #[Then('the checksum of the response content should be :md5')]
    public function checksumOfTheResponseContentShouldBe(string $expectedChecksum): void
    {
        $content = $this->minkContext->getSession()->getPage()->getContent();

        $foundChecksum = md5($content);

        if ($expectedChecksum !== $foundChecksum) {
            throw new ExpectationException(
                sprintf('The checksum %s is not the expected checksum %s', $foundChecksum, $expectedChecksum),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('/^(?:|I )should see tooltip "(?P<value>(?:[^"]|\\")*)"$/')]
    public function shouldSeeTooltip(string $tooltip): void
    {
        $link = $this->minkContext->getSession()->getPage()->find('css', sprintf('a[data-tooltip="%s"]', $tooltip));

        if (null === $link) {
            throw new ExpectationException(
                sprintf('Tooltip "%s" was not found', $tooltip),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('/^(?:|I )should not see tooltip "(?P<value>(?:[^"]|\\")*)"$/')]
    public function shouldNotSeeTooltip(string $tooltip): void
    {
        $link = $this->minkContext->getSession()->getPage()->find('css', sprintf('a[data-tooltip="%s"]', $tooltip));

        if (null !== $link) {
            throw new ExpectationException(
                sprintf('Tooltip "%s" was found', $tooltip),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('/^the downloaded file should (?P<mode>(strictly) )?be the same as "(?P<filename>(?:[^"]|\\")*)"$/')]
    public function assertDownloadedFile(string $mode, string $filename): void
    {
        if ($this->minkContext->getMinkParameter('files_path')) {
            $fullPath = rtrim(realpath($this->minkContext->getMinkParameter('files_path')), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
            if (is_file($fullPath)) {
                $filename = $fullPath;
            }
        }
        $expected = file_get_contents($filename);
        if ('strictly' === trim($mode)) {
            $value = $this->minkContext->getSession()->getDriver()->getContent();
        } else {
            $value = $this->minkContext->getSession()->getPage()->getContent(); // uses trim()
        }

        if ($expected !== $value) {
            throw new ExpectationException(
                sprintf('The downloaded file is not same as "%s"', $filename),
                $this->minkContext->getSession()->getDriver(),
            );
        }

    }

    /**
     * @throws ExpectationException
     * exemples:
     * I should see a red label "oui"
     * I should see a label "ok"
     */
    #[Then('/^(?:|I )should see a (?P<color>(?:[\w])* )?label "(?P<value>(?:[^"]|\\")*)"$/')]
    public function shouldSeeLabel(string $color, string $text): void
    {
        $label = $this->minkContext->getSession()->getPage()->find('css', sprintf('.ui.label%s', $color != '' ? ('.' . $color) : ''));

        if (null === $label) {
            throw new ExpectationException(
                sprintf('label "%s" was not found', $text),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    /**
     * @throws ExpectationException
     * exemples:
     * I should see an image with source "image.png"
     */
    #[Then('/^(?:|I )should see an image with source "(?P<source>(?:[^"]|\\")*)"$/')]
    public function shouldSeeImage(string $source): void
    {
        $image = $this->minkContext->getSession()->getPage()->find('css', sprintf('img[src="%s"]', $source));

        if (null === $image) {
            throw new ExpectationException(
                sprintf('image with source "%s" was not found', $source),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('/^(?:|I )click on link with (class|id) "(?P<text>(?:[^"]|\\")*)"$/')]
    public function clickOnLink(string $type, string $text): void
    {
        $selector = match ($type) {
            'class' => 'a.' . $text,
            'id' => 'a#' . $text,
        };
        $node = $this->minkContext->getSession()->getPage()->find('css', $selector);

        if (null === $node) {
            throw new ExpectationException(
                sprintf('link with %S "%s" was not found', $type, $selector),
                $this->minkContext->getSession()->getDriver(),
            );
        }

        $this->minkContext->getSession()->executeScript('document.querySelector("' . $selector . '").click();');
    }

    #[Then('/^(?:|I )open menu "(?P<text>(?:[^"]|\\")*)"$/')]
    public function openMenu(string $text): void
    {
        $this->minkContext->getSession()->getPage()->find('css', 'div.header.title:contains("' . $text . '")')->click();
    }
}
