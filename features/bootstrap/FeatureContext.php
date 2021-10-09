<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\Process\Process;

class FeatureContext implements Context
{

    /** @var \Behat\MinkExtension\Context\MinkContext */
    private $minkContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->minkContext = $environment->getContext('Behat\MinkExtension\Context\MinkContext');
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
     * @Then the response header :arg1 should equal :arg2
     */
    public function assertResponseHeaderEquals($headerName, $expectedValue)
    {
        $this->minkContext->assertSession()->responseHeaderEquals($headerName, $expectedValue);
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
}
