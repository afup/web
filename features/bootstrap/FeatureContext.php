<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use Behat\MinkExtension\Context\MinkContext;
use Smalot\PdfParser\Parser;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use AppBundle\Event\Model\Event;

class FeatureContext implements Context
{
    const MAILCATCHER_URL = 'http://mailcatcher:1080';

    /** @var MinkContext */
    private $minkContext;

    private $pdfPages = [];

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->minkContext = $environment->getContext(MinkContext::class);
    }

    /**
     * @BeforeScenario @reloadDbWithTestData
     */
    public function beforeScenarioReloadDb()
    {
        $this->resetDb();
        $this->migrateDb();
        $this->seedRun();
    }

    private function resetDb()
    {
        $pdo = new \PDO("mysql:host=dbtest", "root", "root");
        $pdo->exec('DROP DATABASE IF EXISTS web');
        $pdo->exec('CREATE DATABASE web');
    }

    private function migrateDb()
    {
        $this->runCommand( ["./bin/phinx", "migrate", "-e", "test"]);
    }

    private function seedRun()
    {
        $this->runCommand( ["./bin/phinx", "seed:run", "-e", "test"]);
    }

    /**
     * @BeforeScenario @clearAllMailInscriptionAttachments
     */
    public function beforeScenarioClearAllMailInscriptionAttachments()
    {
        $filesystem = new Filesystem();
        $filesystem->remove(Event::getInscriptionAttachmentDir());
    }

    /**
     * @BeforeScenario @clearAllSponsorFiles
     */
    public function beforeScenarioClearAllSponsorFiles()
    {
        $filesystem = new Filesystem();
        $filesystem->remove(Event::getSponsorFileDir());
    }

    private function runCommand(array $command)
    {
        $process = new Process($command);
        $process->mustRun();
    }

    /**
     * @Given I am logged in as admin and on the Administration
     */
    public function iAmLoggedInAsAdminAndOnTheAdministration()
    {
        $this->iAmLoggedInAsAdmin();
        $this->minkContext->clickLink("Administration");
    }

    /**
     * @Given I am logged in as admin
     */
    public function iAmLoggedInAsAdmin()
    {
        $this->iAmLoggedInWithTheUserAndThePassword('admin', 'admin');
    }

    /**
     * @Given I am logged-in with the user :arg1 and the password :arg2
     */
    public function iAmLoggedInWithTheUserAndThePassword($user, $password)
    {
        $this->minkContext->iAmOnHomepage();
        $this->minkContext->assertPageContainsText("Tous les deux mois, des nouvelles de L'AFUP");
        $this->minkContext->clickLink("Se connecter");
        $this->minkContext->assertPageContainsText("Email ou nom d'utilisateur");
        $this->minkContext->fillField("utilisateur", $user);
        $this->minkContext->fillField("mot_de_passe", $password);
        $this->minkContext->pressButton("Se connecter");
        $this->minkContext->assertPageContainsText("Espace membre");
    }

    /**
     * @Then I submit the form with name :formName
     */
    public function submitFormWithName($formName)
    {
        $form = $this->minkContext->getSession()->getPage()->find('xpath', "//form[@name='$formName']");

        if (null === $form) {
            throw new ExpectationException(sprintf('The form named "%s" not found', $formName), null);
        }

        $form->submit();
    }

    /**
     * @Then simulate the Paybox callback
     */
    public function simulateThePayboxCallback()
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
     */
    public function selectHasValues($field, $expectedValuesJson)
    {
        $node = $this->minkContext->assertSession()->fieldExists($field);
        $options = $node->findAll('css', 'option');

        $expectedValues = json_decode($expectedValuesJson, true);

        $foundValues = [];
        foreach ($options as $option) {
            $foundValues[] = $option->getText();
        }

        if ($foundValues != $expectedValues) {
            throw new \Exception(sprintf('The select has the following values %s (expected %s)', json_encode($foundValues, JSON_UNESCAPED_UNICODE), $expectedValuesJson));
        }
    }

    /**
     * @Then The :field field should has the following selected value :expectedValue
     */
    public function selectHasForCurrentSelectedValue($field, $expectedValue)
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

        if ($selectedValue != $expectedValue) {
            throw new \Exception(sprintf('The select has the following value "%s" (expected "%s")', $selectedValue, $expectedValue));
        }
    }


    /**
     * @Then the response header :arg1 should equal :arg2
     */
    public function assertResponseHeaderEquals($headerName, $expectedValue)
    {
        $this->minkContext->assertSession()->responseHeaderEquals($headerName, $expectedValue);
    }

    /**
     * @Then the response header :arg1 should match :arg2
     */
    public function assertResponseHeaderMatch($headerName, $regExpExpectedValue)
    {
        $this->minkContext->assertSession()->responseHeaderMatches($headerName, $regExpExpectedValue);
    }

    /**
     * @When I follow the button of tooltip :arg1
     */
    public function clickLinkOfTooltip($tooltip)
    {
        $link = $this->minkContext->getSession()->getPage()->find('css', sprintf('a[data-tooltip="%s"]', $tooltip));

        if (null === $link) {
            throw new \Exception(sprintf('Link of tooltip "%s" not found',$tooltip));
        }

        $link->click();
    }



    /**
     * @BeforeScenario @clearEmails
     */
    public function clearEmails()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::MAILCATCHER_URL . '/messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \RuntimeException(sprintf('Error : ' . curl_error($ch)));
        }

        curl_close($ch);
    }


    /**
     * @Then I should only receive the following emails:
     */
    public function theFollowingEmailsShoudBeReceived(TableNode $expectedEmails)
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

        if ($foundEmails != $expectedEmailsArray) {
            throw new \Exception(sprintf('The emails are not the expected ones "%s" (expected "%s")', var_export($foundEmails, true), var_export($expectedEmailsArray, true)));
        }
    }

    /**
     * @Then the checksum of the attachment :filename of the message of id :id should be :md5sum
     */
    public function theChecksumOfTheAttachmntOfTheMessagOfIdShouldBe($filename, $id, $md5sum)
    {
        $infos = json_decode(file_get_contents(self::MAILCATCHER_URL . '/messages/' . $id . '.json'), true);

        $cid = null;
        foreach ($infos['attachments'] as $attachment) {
            if ($attachment['filename'] == $filename) {
                $cid = $attachment['cid'];
            }
        }

        if (null === $cid) {
          throw new \Exception(sprintf("Attachment with name %s not found", $filename));
        }

        $attachmentContent = file_get_contents(self::MAILCATCHER_URL . '/messages/' . $id . '/parts/' . $cid);
        $actualMd5sum = md5($attachmentContent);

        if ($actualMd5sum != $md5sum) {
            throw new \Exception(sprintf("The md5sum of %s, if not %s (found %s)", $filename, $md5sum, $actualMd5sum));
        }
    }

    /**
     * @Then the plain text content of the message of id :id should be :
     */
    public function thePlainTextContentOfTheMessageOfIdShouldBe($id, PyStringNode $expectedContent)
    {
        $content = file_get_contents(self::MAILCATCHER_URL . '/messages/' . $id . '.plain');
        $expectedContentString = $expectedContent->getRaw();

        $content = str_replace("\r\n", "\n", $content);

        if ($content != $expectedContentString) {
            throw new \Exception(sprintf("The content \n%s\nis not the expected one \n%s\n", var_export($content, true), var_export($expectedContentString, true)));
        }
    }

    /**
     * @When I parse the pdf downloaded content
     */
    public function iParseThePdfContent()
    {
        $pageContent = $this->minkContext->getSession()->getPage()->getContent();

        $parser = new Parser();
        $pdf    = $parser->parseContent($pageContent);
        $pages  = $pdf->getPages();

        $this->pdfPages = [];
        foreach ($pages as $i => $page) {
            $this->pdfPages[++$i] = $page->getText();
        }
    }

    /**
     * @Then The page :page of the PDF should contain :content
     */
    public function thePageOfThePdfShouldContain($page, $expectedContent)
    {
        $pageContent = isset($this->pdfPages[$page]) ? $this->pdfPages[$page] : null;

        if (false === strpos($pageContent, $expectedContent)) {
            throw new \Exception(sprintf('The content "%s" was not found in the content "%s"', $expectedContent, $pageContent));
        }
    }

    /**
     * @Then The page :page of the PDF should not contain :content
     */
    public function thePageOfThePdfShouldNotContain($page, $expectedContent)
    {
        if (!isset($this->pdfPages[$page])) {
            throw new \Exception(sprintf("The page %d does not exists", $page));
        }

        $pageContent = $this->pdfPages[$page];

        if (false !== strpos($pageContent, $expectedContent)) {
            throw new \Exception(sprintf('The content "%s" was not found in the content "%s"', $expectedContent, $pageContent));
        }
    }

    /**
     * @Then print last PDF content
     */
    public function printLastResponse()
    {
        echo (
            implode("######\n", $this->pdfPages)
        );
    }

    /**
     * @Then the checksum of the response content should be :md5
     */
    public function checksumOfTheResponseContentShouldBe($expectedChecksum)
    {
        $content = $this->minkContext->getSession()->getPage()->getContent();

        $foundChecksum = md5($content);

        if ($expectedChecksum !== $foundChecksum) {
            throw new \Exception(sprintf("The checksum %s is not the expected checksum %s", $foundChecksum, $expectedChecksum));
        }
    }

    /**
     * @Then print last response headers
     */
    public function printLastResponseHeaders()
    {
        $headers = [];
        foreach ($this->minkContext->getSession()->getResponseHeaders() as $name => $values) {
            foreach ($values as $value) {
                $headers[] = sprintf("%s : %s", $name, $value);
            }
        }

        echo implode("\n", $headers);
    }
}
