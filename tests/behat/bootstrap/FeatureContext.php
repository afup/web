<?php

declare(strict_types=1);

use Afup\Tests\Support\DatabaseManager;
use AppBundle\Event\Model\Event;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use Behat\MinkExtension\Context\MinkContext;
use Smalot\PdfParser\Parser;
use Symfony\Component\Filesystem\Filesystem;

class FeatureContext implements Context
{
    private const MAILCATCHER_URL = 'http://mailcatcher:1080';

    private MinkContext $minkContext;

    private array $pdfPages = [];

    private DatabaseManager $databaseManager;

    public function __construct()
    {
        $this->databaseManager = new DatabaseManager(true);
    }

    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        $this->minkContext = $environment->getContext(MinkContext::class);
    }

    /**
     * @BeforeScenario @reloadDbWithTestData
     */
    public function beforeScenarioReloadDatabase(): void
    {
        $this->databaseManager->reloadDatabase();
    }

    /**
     * @BeforeScenario @clearAllMailInscriptionAttachments
     */
    public function beforeScenarioClearAllMailInscriptionAttachments(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove(Event::getInscriptionAttachmentDir());
    }

    /**
     * @BeforeScenario @clearAllSponsorFiles
     */
    public function beforeScenarioClearAllSponsorFiles(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove(Event::getSponsorFileDir());
    }

    /**
     * @Given I am logged in as admin and on the Administration
     */
    public function iAmLoggedInAsAdminAndOnTheAdministration(): void
    {
        $this->iAmLoggedInAsAdmin();
        $this->minkContext->clickLink('Administration');
    }

    /**
     * @Given I am logged in as admin
     */
    public function iAmLoggedInAsAdmin(): void
    {
        $this->iAmLoggedInWithTheUserAndThePassword('admin', 'admin');
    }

    /**
     * @Given I am logged-in with the user :username and the password :password
     */
    public function iAmLoggedInWithTheUserAndThePassword(string $username, string $password): void
    {
        $this->minkContext->visitPath('/admin/login');
        $this->minkContext->fillField('utilisateur', $username);
        $this->minkContext->fillField('mot_de_passe', $password);
        $this->minkContext->pressButton('Se connecter');
        $this->minkContext->assertPageContainsText('Espace membre');
    }

    /**
     * @Then I submit the form with name :formName
     * @throws ExpectationException
     */
    public function submitFormWithName(string $formName): void
    {
        $form = $this->minkContext->getSession()->getPage()->find('xpath', "//form[@name='$formName']");

        if (null === $form) {
            throw new ExpectationException(
                sprintf('The form named "%s" not found', $formName),
                $this->minkContext->getSession()->getDriver()
            );
        }

        $form->submit();
    }

    /**
     * @Then simulate the Paybox callback
     */
    public function simulateThePayboxCallback(): void
    {
        $url = $this->minkContext->getSession()->getCurrentUrl();
        $url = str_replace('paybox-redirect', 'paybox-callback', $url);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_exec($curl);
    }

    /**
     * @Then The :field field should only contain the follow values :expectedValuesJson
     * @throws ExpectationException
     */
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
                    $expectedValuesJson
                ),
                $this->minkContext->getSession()->getDriver()
            );
        }
    }

    /**
     * @Then The :field field should have the following selected value :expectedValue
     * @throws ExpectationException
     */
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
                $this->minkContext->getSession()->getDriver()
            );
        }
    }

    /**
     * @Then The :field field should have the following selected text :expectedValue
     * @throws ExpectationException
     */
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
                $this->minkContext->getSession()->getDriver()
            );
        }
    }


    /**
     * @Then the response header :arg1 should equal :arg2
     * @throws ExpectationException
     */
    public function assertResponseHeaderEquals(string $headerName, string $expectedValue): void
    {
        $this->minkContext->assertSession()->responseHeaderEquals($headerName, $expectedValue);
    }

    /**
     * @Then the response header :arg1 should match :arg2
     * @throws ExpectationException
     */
    public function assertResponseHeaderMatch(string $headerName, string $regExpExpectedValue): void
    {
        $this->minkContext->assertSession()->responseHeaderMatches($headerName, $regExpExpectedValue);
    }

    /**
     * @Then /^the json response has the key "(?P<key>[^"]*)" with value "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertResponseHasJsonKeyAndValue(string $key, string $value): void
    {
        $this->minkContext->assertResponseContains(sprintf('"%s":"%s"', $key, $value));
    }

    /**
     * @Then the current URL should match :arg1
     * @throws ExpectationException
     */
    public function assertCurrentUrlContains(string $regex): void
    {
        $currentUrl = $this->minkContext->getSession()->getCurrentUrl();

        if (!preg_match($regex, $currentUrl)) {
            throw new ExpectationException(
                sprintf('The current URL "%s" does not matches "%s"', $currentUrl, $regex),
                $this->minkContext->getSession()->getDriver()
            );
        }
    }

    /**
     * @When I follow the button of tooltip :arg1
     * @throws ExpectationException
     */
    public function clickLinkOfTooltip(string $tooltip): void
    {
        $link = $this->minkContext->getSession()->getPage()->find('css', sprintf('a[data-tooltip="%s"]', $tooltip));

        if (null === $link) {
            throw new ExpectationException(
                sprintf('Link of tooltip "%s" not found', $tooltip),
                $this->minkContext->getSession()->getDriver()
            );
        }

        $link->click();
    }


    /**
     * @BeforeScenario @clearEmails
     */
    public function clearEmails(): void
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::MAILCATCHER_URL . '/messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        curl_exec($ch);
        if (curl_errno($ch) !== 0) {
            throw new \RuntimeException('Error : ' . curl_error($ch));
        }

        curl_close($ch);
    }


    /**
     * @Then I should only receive the following emails:
     * @throws ExpectationException
     */
    public function theFollowingEmailsShouldBeReceived(TableNode $expectedEmails): void
    {
        $expectedEmailsArray = [];
        foreach ($expectedEmails as $expectedEmail) {
            $expectedEmailsArray[] = [
                'to' => $expectedEmail['to'],
                'subject' => $expectedEmail['subject'],
            ];
        }


        $content = file_get_contents(self::MAILCATCHER_URL . '/messages');
        $decodedContent = json_decode($content, true);

        $foundEmails = [];
        foreach ($decodedContent as $mail) {
            $foundEmails[] = [
                'to' => implode(',', $mail['recipients']),
                'subject' => $mail['subject'],
            ];
        }

        if ($foundEmails !== $expectedEmailsArray) {
            throw new ExpectationException(
                sprintf(
                    'The emails are not the expected ones "%s" (expected "%s")',
                    var_export($foundEmails, true),
                    var_export($expectedEmailsArray, true)
                ),
                $this->minkContext->getSession()->getDriver()
            );
        }
    }

    /**
     * @Then the checksum of the attachment :filename of the message of id :id should be :md5sum
     * @throws ExpectationException
     */
    public function theChecksumOfTheAttachmentOfTheMessageOfIdShouldBe(string $filename, string $id, string $md5sum): void
    {
        $infos = json_decode(file_get_contents(self::MAILCATCHER_URL . '/messages/' . $id . '.json'), true);

        $cid = null;
        foreach ($infos['attachments'] as $attachment) {
            if ($attachment['filename'] === $filename) {
                $cid = $attachment['cid'];
            }
        }

        if (null === $cid) {
            throw new ExpectationException(
                sprintf('Attachment with name %s not found', $filename),
                $this->minkContext->getSession()->getDriver()
            );
        }

        $attachmentContent = file_get_contents(self::MAILCATCHER_URL . '/messages/' . $id . '/parts/' . $cid);
        $actualMd5sum = md5($attachmentContent);

        if ($actualMd5sum !== $md5sum) {
            throw new ExpectationException(
                sprintf('The md5sum of %s, if not %s (found %s)', $filename, $md5sum, $actualMd5sum),
                $this->minkContext->getSession()->getDriver()
            );
        }
    }

    /**
     * @Then the plain text content of the message of id :id should be :
     * @throws ExpectationException
     */
    public function thePlainTextContentOfTheMessageOfIdShouldBe(string $id, PyStringNode $expectedContent): void
    {
        $content = file_get_contents(self::MAILCATCHER_URL . '/messages/' . $id . '.plain');
        $expectedContentString = $expectedContent->getRaw();

        $content = str_replace("\r\n", "\n", $content);

        if ($content !== $expectedContentString) {
            throw new ExpectationException(
                sprintf(
                    "The content \n%s\nis not the expected one \n%s\n",
                    var_export($content, true),
                    var_export($expectedContentString, true)
                ),
                $this->minkContext->getSession()->getDriver()
            );
        }
    }

    /**
     * @When I parse the pdf downloaded content
     */
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

    /**
     * @Then The page :page of the PDF should contain :content
     * @throws ExpectationException
     */
    public function thePageOfThePdfShouldContain(string $page, string $expectedContent): void
    {
        $pageContent = $this->pdfPages[$page] ?? null;

        if (!str_contains($pageContent, $expectedContent)) {
            throw new ExpectationException(
                sprintf('The content "%s" was not found in the content "%s"', $expectedContent, $pageContent),
                $this->minkContext->getSession()->getDriver()
            );
        }
    }

    /**
     * @Then The page :page of the PDF should not contain :content
     * @throws ExpectationException
     */
    public function thePageOfThePdfShouldNotContain(string $page, string $expectedContent): void
    {
        if (!isset($this->pdfPages[$page])) {
            throw new ExpectationException(
                sprintf('The page %d does not exists', $page),
                $this->minkContext->getSession()->getDriver()
            );
        }

        $pageContent = $this->pdfPages[$page];

        if (str_contains($pageContent, $expectedContent)) {
            throw new ExpectationException(
                sprintf('The content "%s" was not found in the content "%s"', $expectedContent, $pageContent),
                $this->minkContext->getSession()->getDriver()
            );
        }
    }

    /**
     * @Then the checksum of the response content should be :md5
     * @throws ExpectationException
     */
    public function checksumOfTheResponseContentShouldBe(string $expectedChecksum): void
    {
        $content = $this->minkContext->getSession()->getPage()->getContent();

        $foundChecksum = md5($content);

        if ($expectedChecksum !== $foundChecksum) {
            throw new ExpectationException(
                sprintf('The checksum %s is not the expected checksum %s', $foundChecksum, $expectedChecksum),
                $this->minkContext->getSession()->getDriver()
            );
        }
    }

    /**
     * @Then print last PDF content
     */
    public function printLastPDFResponse(): void
    {
        echo implode("######\n", $this->pdfPages);
    }

    /**
     * @Then print last response headers
     */
    public function printLastResponseHeaders(): void
    {
        $headers = [];
        foreach ($this->minkContext->getSession()->getResponseHeaders() as $name => $values) {
            foreach ($values as $value) {
                $headers[] = sprintf('%s : %s', $name, $value);
            }
        }

        echo implode("\n", $headers);
    }

    /**
     * @When I request a password reset for :arg1
     */
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

    /**
     * @Then I should receive an email
     */
    public function iShouldReceiveAnEmail(): void
    {
        $content = file_get_contents(self::MAILCATCHER_URL . '/messages');
        $emails = json_decode($content, true);

        if (count($emails) !== 1) {
            throw new ExpectationException(
                'The email has not been received.',
                $this->minkContext->getSession()->getDriver()
            );
        }
    }

    /**
     * @Then the email should contain a full URL starting with :arg1
     */
    public function theEmailShouldContainAFullUrlStartingWith(string $arg1): void
    {
        $content = file_get_contents(self::MAILCATCHER_URL . '/messages');
        $decodedContent = json_decode($content, true);

        $foundEmails = [];
        foreach ($decodedContent as $mail) {
            $foundEmails[] = [
                'id' => $mail['id'],
                'to' => $mail['to'] ?? $mail['recipients'][0],
                'subject' => $mail['subject'],
            ];
        }

        if (count($foundEmails) !== 1) {
            throw new ExpectationException(
                'The email has not been received.',
                $this->minkContext->getSession()->getDriver()
            );
        }

        $content = file_get_contents(self::MAILCATCHER_URL . '/messages/' . $foundEmails[0]['id'] . '.plain');
        if (!str_contains($content, $arg1)) {
            throw new ExpectationException(
                sprintf(
                    'The email content does not contain the expected URL "%s" (expected "%s")',
                    $content,
                    $arg1
                ), $this->minkContext->getSession()->getDriver()
            );
        }
    }
}
