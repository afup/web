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
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use DateTimeImmutable;
use DateTimeInterface;
use Smalot\PdfParser\Parser;

class FeatureContext implements Context
{
    use ApiContext;
    use TimeContext;
    use EmailContext;

    private MinkContext $minkContext;

    private array $pdfPages = [];

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

    #[BeforeScenario('reloadDbWithTestData')]
    public function beforeScenarioReloadDatabase(): void
    {
        $this->databaseManager->reloadDatabase();
    }

    #[Given('I am logged in as admin and on the Administration')]
    public function iAmLoggedInAsAdminAndOnTheAdministration(): void
    {
        $this->iAmLoggedInAsAdmin();
        $this->minkContext->clickLink('Administration');
    }

    #[Given('I am logged in as admin')]
    public function iAmLoggedInAsAdmin(): void
    {
        $this->iAmLoggedInWithTheUserAndThePassword('admin', 'admin');
    }

    #[Given('I am logged-in with the user :username and the password :password')]
    public function iAmLoggedInWithTheUserAndThePassword(string $username, string $password): void
    {
        $this->minkContext->visitPath('/admin/login');
        $this->minkContext->fillField('utilisateur', $username);
        $this->minkContext->fillField('mot_de_passe', $password);
        $this->minkContext->pressButton('Se connecter');
        $this->minkContext->assertPageContainsText('Espace membre');
    }

    #[Then('I submit the form with name :formName')]
    public function submitFormWithName(string $formName): void
    {
        $form = $this->minkContext->getSession()->getPage()->find('xpath', "//form[@name='$formName']");

        if (null === $form) {
            throw new ExpectationException(
                sprintf('The form named "%s" not found', $formName),
                $this->minkContext->getSession()->getDriver(),
            );
        }

        $form->submit();
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

    #[Then('The :field field should only contain the follow values :expectedValuesJson')]
    public function selectHasValues(string $field, string $expectedValuesJson): void
    {
        $node = $this->minkContext->assertSession()->fieldExists($field);
        $options = $node->findAll('css', 'option');

        $expectedValues = json_decode($expectedValuesJson, true);

        $foundValues = [];
        foreach ($options as $option) {
            $foundValues[] = $option->getText();
        }

        if ($foundValues !== $expectedValues) {
            throw new ExpectationException(
                sprintf(
                    'The select has the following values %s (expected %s)',
                    json_encode($foundValues, JSON_UNESCAPED_UNICODE),
                    $expectedValuesJson,
                ),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('The :field field should have the following selected value :expectedValue')]
    public function selectHasForCurrentSelectedValue(string $field, string $expectedValue): void
    {
        $node = $this->minkContext->assertSession()->fieldExists($field);
        $options = $node->findAll('css', 'option');

        $selectedValue = null;
        foreach ($options as $option) {
            if ($option->isSelected()) {
                $selectedValue = $option->getValue();
                break;
            }
        }

        if ($selectedValue !== $expectedValue) {
            throw new ExpectationException(
                sprintf('The select has the following value "%s" (expected "%s")', $selectedValue, $expectedValue),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('The :field field should have the following selected text :expectedValue')]
    public function selectHasForCurrentSelectedText(string $field, string $expectedValue): void
    {
        $node = $this->minkContext->assertSession()->fieldExists($field);
        $options = $node->findAll('css', 'option');

        $selectedValue = null;
        foreach ($options as $option) {
            if ($option->isSelected()) {
                $selectedValue = $option->getText();
                break;
            }
        }

        if ($selectedValue !== $expectedValue) {
            throw new ExpectationException(
                sprintf('The select has the following text "%s" (expected "%s")', $selectedValue, $expectedValue),
                $this->minkContext->getSession()->getDriver(),
            );
        }
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

    #[When('I parse the pdf downloaded content')]
    public function iParseThePdfContent(): void
    {
        $pageContent = $this->minkContext->getSession()->getPage()->getContent();

        $parser = new Parser();
        $pdf = $parser->parseContent($pageContent);
        $pages = $pdf->getPages();

        $this->pdfPages = [];
        foreach ($pages as $i => $page) {
            $this->pdfPages[++$i] = $page->getText();
        }
    }

    #[Then('The page :page of the PDF should contain :content')]
    public function thePageOfThePdfShouldContain(string $page, string $expectedContent): void
    {
        $pageContent = $this->pdfPages[$page] ?? null;

        if (!str_contains((string) $pageContent, $expectedContent)) {
            throw new ExpectationException(
                sprintf('The content "%s" was not found in the content "%s"', $expectedContent, $pageContent),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('The page :page of the PDF should not contain :content')]
    public function thePageOfThePdfShouldNotContain(string $page, string $expectedContent): void
    {
        if (!isset($this->pdfPages[$page])) {
            throw new ExpectationException(
                sprintf('The page %d does not exists', $page),
                $this->minkContext->getSession()->getDriver(),
            );
        }

        $pageContent = $this->pdfPages[$page];

        if (str_contains($pageContent, $expectedContent)) {
            throw new ExpectationException(
                sprintf('The content "%s" was not found in the content "%s"', $expectedContent, $pageContent),
                $this->minkContext->getSession()->getDriver(),
            );
        }
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

    #[When('I request a password reset for :arg1')]
    public function iRequestAPasswordReset(string $arg1): void
    {
        $this->minkContext->iAmOnHomepage();
        $this->minkContext->assertPageContainsText("Tous les trois mois, des nouvelles de L'AFUP");
        $this->minkContext->clickLink("Se connecter");
        $this->minkContext->assertPageContainsText("Email ou nom d'utilisateur");
        $this->minkContext->clickLink("Mot de passe perdu");
        $this->minkContext->assertPageContainsText("Mot de passe perdu");
        $this->minkContext->fillField("form_email", $arg1);
        $this->minkContext->pressButton("Demander un nouveau mot de passe");
        $this->minkContext->assertPageContainsText("Votre demande a été prise en compte. Si un compte correspond à cet email vous recevez un nouveau mot de passe rapidement.");
    }

    #[Then('the response should contain date :arg1')]
    #[Then('the response should contain date :arg1 with format :arg2')]
    public function responseShouldContainsDate(string $datetimeFormat, string $withFormat = DateTimeInterface::ATOM): void
    {
        $this->minkContext->assertResponseContains((new DateTimeImmutable($datetimeFormat))->format($withFormat));
    }

    #[When('/^(?:|I )fill hidden field "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/')]
    #[When('/^(?:|I )fill hidden field "(?P<field>(?:[^"]|\\")*)" with:$/')]
    #[When('/^(?:|I )fill hidden field "(?P<value>(?:[^"]|\\")*)" for "(?P<field>(?:[^"]|\\")*)"$/')]
    public function fillHiddenField($field, $value): void
    {
        $this->minkContext->getSession()->getPage()
            ->find('css', 'input[name="' . $field . '"]')
            ?->setValue($value);
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

    #[Then('/^the downloaded file should be the same as "(?P<value>(?:[^"]|\\")*)"$/')]
    public function assertDownloadedFile(string $filename): void
    {
        if ($this->minkContext->getMinkParameter('files_path')) {
            $fullPath = rtrim(realpath($this->minkContext->getMinkParameter('files_path')), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
            if (is_file($fullPath)) {
                $filename = $fullPath;
            }
        }
        $expected = file_get_contents($filename);
        $value = $this->minkContext->getSession()->getPage()->getContent();

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
}
